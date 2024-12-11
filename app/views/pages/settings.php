<?php

use App\Utils\Database\Setting;

/** @var Setting[] $settings */
$settings ??= [];

$ticTacToeRestrictionTimeFrom = find($settings, fn($setting) => $setting->get('key') === 'tic-tac-toe-from')?->get('value') ?? '';
$ticTacToeRestrictionTimeTo = find($settings, fn($setting) => $setting->get('key') === 'tic-tac-toe-to')?->get('value') ?? '';

?>

<h1 class="mb-4 text-5xl">Settings</h1>

<div class="mt-10">
    <form id="settings-form">
        <h2 class="text-3xl">TicTacToe</h2>
        <div class="flex items-center w-full gap-6 mt-2">
            <div>
                <label for="tic-tac-toe-restriction-time-from" class="block font-medium text-sm/6">TicTacToe ab</label>
                <input value="<?php echo $ticTacToeRestrictionTimeFrom; ?>" type="time" name="tic-tac-toe-restriction-time-from" id="tic-tac-toe-restriction-time-from" class="block min-w-0 grow py-1.5 pl-1 pr-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6 rounded-md">
            </div>
            <span>BIS</span>
            <div>
                <label for="tic-tac-toe-restriction-time-to" class="block font-medium text-sm/6">TicTacToe bis</label>
                <input value="<?php echo $ticTacToeRestrictionTimeTo; ?>" type="time" name="tic-tac-toe-restriction-time-to" id="tic-tac-toe-restriction-time-to" class="block min-w-0 grow py-1.5 pl-1 pr-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6 rounded-md">
            </div>
        </div>

        <div class="mt-10">
            <button type="submit" class="p-2 px-4 text-white bg-blue-500 rounded-lg hover:cursor-pointer">Save</button>
        </div>
    </form>
</div>

<script>
    const form = document.getElementById('settings-form');
    const tttFrom = document.getElementById('tic-tac-toe-restriction-time-from');
    const tttTo = document.getElementById('tic-tac-toe-restriction-time-to');

    form.addEventListener('submit', event => {
        event.preventDefault();

        fetch('/api/settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'auth': "<?php echo $user->get('api_key'); ?>"
                },
                body: JSON.stringify({
                    ttt_from: tttFrom.value,
                    ttt_to: tttTo.value
                })
            })
            .then(response => response.json())
            .then(data => {
                window.location.reload();
            });
    });
</script>