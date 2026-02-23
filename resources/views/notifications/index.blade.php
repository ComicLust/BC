@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Bildirimlerim</h1>
    </div>
    <div class="space-y-4">
        @forelse($notifications as $notification)
            <div class="card bg-base-100 shadow-xl {{ $notification->is_read ? 'opacity-75' : '' }}">
                <div class="card-body">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="card-title">{{ $notification->message }}</h2>
                            <p class="text-sm text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-2">
                            @if(!$notification->is_read)
                                <form method="POST" action="{{ route('notifications.mark-as-read', $notification) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-outline">Okundu</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-error">Sil</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400">Henüz bildiriminiz yok.</div>
        @endforelse
    </div>
</div>
@endsection 