<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Riwayat Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
            border-radius: 50px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #3a0ca3;
            border-color: #3a0ca3;
            transform: scale(1.05);
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.3);
            border-color: #4361ee;
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
        }

        .table-hover tbody tr {
            transition: all 0.3s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .status-badge:hover {
            transform: scale(1.1);
        }

        .summary-card {
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg p-4 mb-5 animate-fade-in">
                    <h2 class="text-center mb-4 fw-bold text-primary"><i class="fas fa-search me-2"></i>Cari Riwayat
                        Absensi Siswa</h2>

                    <form action="{{ route('presensi.search') }}" method="GET">
                        <div class="mb-4">
                            <label for="nisn" class="form-label"><i class="fas fa-id-card me-2"></i>NISN
                                Siswa</label>
                            <input type="text" class="form-control" id="nisn" name="nisn"
                                placeholder="Contoh: 1234567890" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="start_date" class="form-label"><i
                                        class="fas fa-calendar-alt me-2"></i>Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="end_date" class="form-label"><i class="fas fa-calendar-alt me-2"></i>Tanggal
                                    Selesai</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold"><i
                                class="fas fa-search me-2"></i>Cari Riwayat</button>
                    </form>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger mt-4 animate-fade-in">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if (isset($student))
                    <div class="card shadow-lg p-4 mt-4 animate-fade-in">
                        <h2 class="text-center mb-4 fw-bold text-primary"><i class="fas fa-history me-2"></i>Riwayat
                            Absensi {{ $student->name }}</h2>
                        <div class="mt-5">
                            <h4 class="mb-4 fw-bold text-primary"><i class="fas fa-chart-pie me-2"></i>Ringkasan Absensi
                            </h4>
                            <div class="row">
                                @foreach ($statusSummary as $status => $count)
                                    <div class="col-md-3 col-sm-6 mb-4">
                                        <div class="card text-center summary-card">
                                            <div class="card-body">
                                                <h5 class="card-title fw-bold">{{ $status }}</h5>
                                                <p class="card-text display-4 fw-bold text-primary">{{ $count }}
                                                </p>
                                                <p class="card-text text-muted">kali</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendance as $record)
                                        <tr>
                                            <td>{{ $record->date }}</td>
                                            <td>
                                                <span
                                                    class="status-badge
                                                    @if ($record->status == 'Hadir') bg-success text-white
                                                    @elseif($record->status == 'Izin') bg-warning text-dark
                                                    @elseif($record->status == 'Sakit') bg-info text-white
                                                    @else bg-danger text-white @endif">
                                                    {{ $record->status }}
                                                </span>
                                            </td>
                                            <td>{{ strip_tags($record->notes) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
