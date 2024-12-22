<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Email Masuk</h1>
        <div class="bg-white rounded-lg shadow-md p-6">
            @forelse($emails as $email)
                <div class="border-b border-gray-200 py-4">
                    <div class="font-medium">Subject: {{ $email['subject'] }}</div>
                    <div class="text-sm text-gray-600">From: {{ $email['from'] }}</div>
                    <div class="text-sm text-gray-500">Received at: {{ $email['date'] }}</div>
                </div>
            @empty
                <p class="text-gray-500">Belum ada email masuk.</p>
            @endforelse
        </div>
    </div>
</body>
</html>
