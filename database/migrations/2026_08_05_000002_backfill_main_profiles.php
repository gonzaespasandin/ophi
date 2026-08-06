<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $users = DB::table('users')->select('id', 'name')->get();

        foreach ($users as $user) {
            $alreadyHasMain = DB::table('profiles')
                ->where('owner_id', $user->id)
                ->where('is_main', true)
                ->exists();

            if ($alreadyHasMain) {
                continue;
            }

            $oldest = DB::table('profiles')
                ->where('owner_id', $user->id)
                ->orderBy('created_at')
                ->orderBy('id')
                ->first();

            if ($oldest) {
                DB::table('profiles')->where('id', $oldest->id)->update(['is_main' => true]);

                continue;
            }

            DB::table('profiles')->insert([
                'name' => $user->name,
                'owner_id' => $user->id,
                'user_id' => $user->id,
                'is_main' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
    }
};
