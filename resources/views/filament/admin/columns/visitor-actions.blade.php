@php
    $visitor = $getRecord();
@endphp

<div class="w-full">
    <a
        href="javascript:void(0)"
        @click.stop="
            $wire.mountAction('sendReviewLink', {}, {
                recordKey: '{{ $visitor->getKey() }}',
                table: true
            })
        "
        class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-950 transition no-underline"
        title="Kirim link review ke WhatsApp wisatawan"
    >
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span>Kirim Review</span>
    </a>
</div>
