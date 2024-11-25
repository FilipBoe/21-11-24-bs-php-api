<?php
$user ??= [];


?>

<form class="max-w-md p-8 mx-auto bg-white rounded-lg shadow-md">
    <div class="mb-4">
        <label for="email" class="block mb-2 text-sm font-bold text-gray-700">Email</label>
        <input type="text" name="email" id="email" placeholder="Email" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
    </div>
    <div class="mb-6">
        <label for="password" class="block mb-2 text-sm font-bold text-gray-700">Password</label>
        <input type="password" name="password" id="password" placeholder="Password" class="w-full px-3 py-2 mb-3 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
    </div>

    <!-- error -->
    <div class="mb-6">
        <p class="text-red-500" id="error-message"><?php echo $error ?? ''; ?></p>
    </div>

    <div class="flex items-center justify-between">
        <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 focus:outline-none focus:shadow-outline">Login</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const errorMessage = document.getElementById('error-message');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            errorMessage.innerText = '';

            const formData = new FormData(form);

            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData)),
            });

            const data = await response.json();

            if (response.status === 401) {
                errorMessage.innerText = data.error;
                return;
            }

            if (response.ok) {
                window.location.href = '/';
            }
        });
    });
</script>