<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{

    public function index(Request $request): Response
{
    abort_unless(
        $request->user()->role?->nama_role === 'Admin',
        403
    );

    return Inertia::render('users/Index', [
        'users' => DB::table('dbo.data_users')
            ->orderBy('nama_users')
            ->get([
                'id_users',
                'nama_users',
                'nama_role',
                'nama_jabatan',
                'nama_tahun_ajaran',
                'email',
                'no_hp',
            ]),
    ]);
}

    public function create(Request $request): Response
    {
        abort_unless(
            $request->user()->role?->nama_role === 'Admin',
            403
        );

        return Inertia::render('users/Create', [
            'roles' => Role::query()
                ->where('status_delete', false)
                ->orderBy('nama_role')
                ->get(['id_role', 'nama_role']),

            'tahunAjaran' => TahunAjaran::query()
                ->where('status_delete', false)
                ->orderByDesc('nama_tahun_ajaran')
                ->get(['id_tahun_ajaran', 'nama_tahun_ajaran']),
        ]);
    }
}
