@php
    $state = $getState();
    $messages = $state['messages'] ?? collect();
    $customerId = $state['customerId'] ?? null;
@endphp

<style>
    .tc-thread {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
    }

    .tc-row {
        display: flex;
    }

    .tc-row--customer { justify-content: flex-start; }
    .tc-row--staff { justify-content: flex-end; }

    .tc-bubble {
        max-width: 85%;
        border-radius: 0.875rem;
        padding: 0.75rem 1rem;
        border: 1px solid;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    /* Light mode */
    .tc-bubble--customer {
        background-color: #f9fafb;
        border-color: #e5e7eb;
    }

    .tc-bubble--staff {
        background-color: rgb(var(--primary-50, 239 246 255));
        border-color: rgb(var(--primary-500, 59 130 246) / 0.35);
    }

    /* Dark mode (Filament toggles `.dark` on <html>) */
    .dark .tc-bubble--customer {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .dark .tc-bubble--staff {
        background-color: rgb(var(--primary-400, 96 165 250) / 0.12);
        border-color: rgb(var(--primary-400, 96 165 250) / 0.3);
    }

    .tc-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 0.375rem;
        font-size: 0.75rem;
        line-height: 1rem;
    }

    .tc-author {
        font-weight: 600;
        color: #111827;
    }

    .dark .tc-author { color: #f9fafb; }

    .tc-chip {
        border-radius: 9999px;
        padding: 0.0625rem 0.5rem;
        font-size: 0.625rem;
        font-weight: 500;
        line-height: 1rem;
    }

    .tc-chip--customer {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .dark .tc-chip--customer {
        background-color: rgba(255, 255, 255, 0.1);
        color: #d1d5db;
    }

    .tc-chip--staff {
        background-color: rgb(var(--primary-100, 219 234 254));
        color: rgb(var(--primary-700, 29 78 216));
    }

    .dark .tc-chip--staff {
        background-color: rgb(var(--primary-400, 96 165 250) / 0.2);
        color: rgb(var(--primary-300, 147 197 253));
    }

    .tc-time { color: #9ca3af; }

    .tc-body {
        white-space: pre-wrap;
        word-break: break-word;
        font-size: 0.875rem;
        line-height: 1.5rem;
        color: #374151;
    }

    .dark .tc-body { color: #e5e7eb; }

    .tc-empty {
        font-size: 0.875rem;
        color: #6b7280;
    }
</style>

<div class="tc-thread">
    @forelse ($messages as $message)
        @php $fromCustomer = $message->user_id === $customerId; @endphp

        <div class="tc-row {{ $fromCustomer ? 'tc-row--customer' : 'tc-row--staff' }}">
            <div class="tc-bubble {{ $fromCustomer ? 'tc-bubble--customer' : 'tc-bubble--staff' }}">
                <div class="tc-meta">
                    <span class="tc-author">{{ $message->author?->name ?? 'Άγνωστος' }}</span>
                    <span class="tc-chip {{ $fromCustomer ? 'tc-chip--customer' : 'tc-chip--staff' }}">
                        {{ $fromCustomer ? 'Πελάτης' : 'Διαχείριση' }}
                    </span>
                    <span class="tc-time">{{ $message->created_at->diffForHumans() }}</span>
                </div>

                <div class="tc-body">{{ $message->body }}</div>
            </div>
        </div>
    @empty
        <p class="tc-empty">Δεν υπάρχουν ακόμη μηνύματα.</p>
    @endforelse
</div>
