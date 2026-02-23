@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Kullanıcılar</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            Yeni Kullanıcı
        </a>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ad</th>
                            <th>E-posta</th>
                            <th>Admin</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-{{ $user->is_admin ? 'success' : 'ghost' }}">
                                    {{ $user->is_admin ? 'Evet' : 'Hayır' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary">
                                        Düzenle
                                    </a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                                            Sil
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 