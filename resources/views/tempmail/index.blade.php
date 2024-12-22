<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temp Mail SYAWAL</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-morphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.125);
        }
        .email-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .email-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto glass-morphism p-8 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-white flex items-center">
                    <i class="fas fa-envelope-open-text mr-3 text-purple-300"></i>
                    Temp Mail WAL
                </h1>
                <div class="text-sm text-gray-300">
                    <i class="fas fa-shield-alt mr-2"></i>Secure & Anonymous
                </div>
            </div>
            
            @if (session('success'))
                <div class="bg-green-500 text-white px-4 py-3 rounded-lg mb-4 flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="bg-red-500 text-white px-4 py-3 rounded-lg mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center">
                                <i class="fas fa-exclamation-triangle mr-3"></i>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (!$tempMail)
                <form action="{{ route('tempmail.generate') }}" method="POST" class="text-center">
                    @csrf
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-full transition duration-300 transform hover:scale-105 flex items-center justify-center mx-auto">
                        <i class="fas fa-magic mr-3"></i>
                        Generate Temporary Email
                    </button>
                </form>
            @else
                <div class="bg-white bg-opacity-10 rounded-lg p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xl text-white">
                                <i class="fas fa-at mr-2 text-purple-300"></i>
                                {{ $tempMail->email }}
                            </p>
                            <p class="text-sm text-gray-300">
                                <i class="fas fa-clock mr-2"></i>
                                Expires: {{ $tempMail->expires_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="checkEmails()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full transition duration-300 flex items-center">
                                <i class="fas fa-sync mr-2"></i>Check Emails
                            </button>
                            
                            <form action="{{ route('tempmail.destroy', $tempMail) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full transition duration-300 flex items-center">
                                    <i class="fas fa-trash-alt mr-2"></i>Deactivate
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div id="emails-container" class="space-y-4">
                    <!-- Emails will be loaded here -->
                </div>
            @endif
        </div>
    </div>

    <script>
        function checkEmails() {
            fetch('{{ route('tempmail.check') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const container = document.getElementById('emails-container');
                        container.innerHTML = ''; // Clear existing emails
                        
                        data.emails.forEach(email => {
                            container.innerHTML += `
                                <div class="email-card bg-white bg-opacity-10 border border-white border-opacity-20 rounded-lg p-5 text-white">
                                    <div class="flex justify-between items-center mb-3">
                                        <div>
                                            <p class="font-bold text-purple-300"><i class="fas fa-user mr-2"></i>${email.from}</p>
                                            <p class="text-lg">${email.subject}</p>
                                        </div>
                                        <p class="text-sm text-gray-400"><i class="fas fa-calendar-alt mr-2"></i>${email.received_at}</p>
                                    </div>
                                    <div class="mt-2">
                                        <p>${email.body}</p>
                                    </div>
                                    ${email.has_attachments ? '<p class="mt-2 text-blue-300"><i class="fas fa-paperclip mr-2"></i>Has attachments</p>' : ''}
                                </div>
                            `;
                        });
                    } else {
                        alert(data.message || 'Failed to check emails');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to check emails');
                });
        }

        // Check emails automatically every 30 seconds
        if (document.getElementById('emails-container')) {
            setInterval(checkEmails, 30000);
            checkEmails(); // Check immediately on page load
        }
    </script>
    <!-- Footer Credit -->
<footer class="fixed bottom-0 left-0 right-0 p-4 text-center">
    <div class="text-white text-sm opacity-70 hover:opacity-100 transition-all duration-300">
        <span class="mr-2">✨</span>
        Developed with 💜 by 
        <span class="font-bold text-purple-300 ml-1">syawalmods</span>
        <span class="ml-2">🚀</span>
    </div>
</footer>
</body>
</html>