@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $project->name }} - Metrikler</h1>
        <button class="btn btn-primary" onclick="document.getElementById('addMetricModal').showModal()">
            Yeni Metrik Ekle
        </button>
    </div>

    @foreach($metrics as $type => $typeMetrics)
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body">
            <h2 class="card-title">{{ ucfirst(str_replace('_', ' ', $type)) }}</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Değer</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($typeMetrics as $metric)
                        <tr>
                            <td>{{ $metric->date->format('d.m.Y') }}</td>
                            <td>{{ $metric->value }}</td>
                            <td>
                                <form action="{{ route('projects.metrics.destroy', [$project, $metric]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-error btn-sm" onclick="return confirm('Bu metriği silmek istediğinizden emin misiniz?')">
                                        Sil
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    @if($metrics->isEmpty())
    <div class="alert alert-info">
        <span>Henüz metrik eklenmemiş.</span>
    </div>
    @endif
</div>

<dialog id="addMetricModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Yeni Metrik Ekle</h3>
        <form action="{{ route('projects.metrics.store', $project) }}" method="POST">
            @csrf
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Metrik Tipi</span>
                </label>
                <select name="type" class="select select-bordered" required>
                    <option value="domain_authority">Domain Authority</option>
                    <option value="page_authority">Page Authority</option>
                    <option value="spam_score">Spam Score</option>
                    <option value="moz_rank">Moz Rank</option>
                </select>
            </div>
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Değer</span>
                </label>
                <input type="number" name="value" step="0.01" class="input input-bordered" required>
            </div>
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Tarih</span>
                </label>
                <input type="date" name="date" class="input input-bordered" required>
            </div>
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Ekle</button>
                <button type="button" class="btn" onclick="document.getElementById('addMetricModal').close()">İptal</button>
            </div>
        </form>
    </div>
</dialog>
@endsection 