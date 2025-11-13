<!-- Flash Toasts -->
<div 
    x-data="{
        show: {{ session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info') ? 'true' : 'false' }},
        type: '{{ session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : (session('info') ? 'info' : ''))) }}',
        message: `{!! session('success') ?? session('error') ?? session('warning') ?? session('info') ?? '' !!}`
    }"
    x-show="show"
    x-init="if(show){ setTimeout(() => show = false, 5500) }"
    x-transition
    class="fixed top-6 right-6 z-50"
    style="min-width: 250px; max-width: 400px;"
>
    <template x-if="type === 'success'">
        <div class="bg-green-100 text-green-800 p-4 rounded-lg shadow mb-4 flex items-start gap-2">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span x-text="message" class="whitespace-normal break-words"></span>
            <button @click="show = false" class="ml-auto text-green-700 hover:text-green-900 flex-shrink-0">&times;</button>
        </div>
    </template>
    <template x-if="type === 'error'">
        <div class="bg-red-100 text-red-800 p-4 rounded-lg shadow mb-4 flex items-start gap-2">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span x-text="message" class="whitespace-normal break-words"></span>
            <button @click="show = false" class="ml-auto text-red-700 hover:text-red-900 flex-shrink-0">&times;</button>
        </div>
    </template>
    <template x-if="type === 'warning'">
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg shadow mb-4 flex items-start gap-2">
            <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/></svg>
            <span x-text="message" class="whitespace-normal break-words"></span>
            <button @click="show = false" class="ml-auto text-yellow-700 hover:text-yellow-900 flex-shrink-0">&times;</button>
        </div>
    </template>
    <template x-if="type === 'info'">
        <div class="bg-blue-100 text-blue-800 p-4 rounded-lg shadow mb-4 flex items-start gap-2">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/></svg>
            <span x-text="message" class="whitespace-normal break-words"></span>
            <button @click="show = false" class="ml-auto text-blue-700 hover:text-blue-900 flex-shrink-0">&times;</button>
        </div>
    </template>
</div>