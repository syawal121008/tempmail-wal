<?php

namespace App\Http\Controllers;

use App\TempMail;
use App\Email;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TempMailController extends Controller
{
    protected $defaultExpiryHours = 24;
    protected $maxEmailsPerPage = 50;

    public function index()
    {
        $tempMail = TempMail::active()->latest()->first();
        $emails = $tempMail 
            ? $tempMail->emails()
                ->orderBy('received_at', 'desc')
                ->paginate($this->maxEmailsPerPage)
            : collect();
        
        return view('tempmail.index', compact('tempMail', 'emails'));
    }

    public function generateEmail(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Deactivate old temporary emails for this user
            if ($request->user()) {
                TempMail::where('user_id', $request->user()->id)
                    ->update(['expires_at' => now()]);
            }
            
            // Get random email from 1secmail
            $response = Http::get('https://www.1secmail.com/api/v1/', [
                'action' => 'genRandomMailbox',
                'count' => 1
            ]);
            
            $email = $response->json()[0];
            
            // Create new temporary email
            TempMail::create([
                'email' => $email,
                'user_id' => $request->user() ? $request->user()->id : null,
                'ip_address' => $request->ip(),
                'expires_at' => now()->addHours($this->defaultExpiryHours),
            ]);
            
            DB::commit();
            
            return redirect()->route('tempmail.index')
                ->with('success', 'Email sementara berhasil dibuat: ' . $email);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to generate temporary email: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal membuat email sementara. Silakan coba lagi.']);
        }
    }

    public function checkEmails(Request $request)
    {
        try {
            $tempMail = TempMail::active()->latest()->first();
            
            if (!$tempMail) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada email sementara yang aktif',
                    'emails' => []
                ]);
            }

            // Parse email address
            [$login, $domain] = explode('@', $tempMail->email);
            
            // Get emails from 1secmail API
            $response = Http::get('https://www.1secmail.com/api/v1/', [
                'action' => 'getMessages',
                'login' => $login,
                'domain' => $domain
            ]);
            
            $messages = $response->json();
            
            foreach ($messages as $message) {
                // Skip if email already exists
                if (Email::where('message_id', $message['id'])->exists()) {
                    continue;
                }
                
                // Get email content
                $content = Http::get('https://www.1secmail.com/api/v1/', [
                    'action' => 'readMessage',
                    'login' => $login,
                    'domain' => $domain,
                    'id' => $message['id']
                ])->json();
                
                // Store email in database
                Email::create([
                    'temp_mail_id' => $tempMail->id,
                    'message_id' => $message['id'],
                    'from' => $message['from'],
                    'subject' => $message['subject'],
                    'body' => $content['textBody'] ?? $content['htmlBody'] ?? '(No content)',
                    'has_attachments' => !empty($content['attachments']),
                    'received_at' => Carbon::parse($message['date']),
                ]);
            }
            
            // Get updated emails from database
            $emails = $tempMail->emails()
                ->orderBy('received_at', 'desc')
                ->limit($this->maxEmailsPerPage)
                ->get()
                ->map(function ($email) {
                    return [
                        'id' => $email->id,
                        'from' => $email->from,
                        'subject' => $email->subject,
                        'body' => $email->body,
                        'has_attachments' => $email->has_attachments,
                        'received_at' => $email->received_at->diffForHumans(),
                        'received_at_formatted' => $email->received_at->format('Y-m-d H:i:s'),
                    ];
                });
                
            return response()->json([
                'status' => 'success',
                'emails' => $emails,
                'last_check' => now()->toIso8601String()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to check emails: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memeriksa email baru',
                'emails' => []
            ], 500);
        }
    }

    public function destroy(TempMail $tempMail)
    {
        try {
            $tempMail->update(['expires_at' => now()]);
            return redirect()->route('tempmail.index')
                ->with('success', 'Email sementara berhasil dinonaktifkan');
        } catch (\Exception $e) {
            Log::error('Failed to deactivate temporary email: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menonaktifkan email sementara']);
        }
    }
}