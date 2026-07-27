<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->createAccessTables();
        $this->convertPlansAndSubscriptions();
        $this->convertNewsletter();
        $this->createEngagementTables();
        $this->convertProfiles();
        $this->convertIngredients();
        $this->convertProducts();
        $this->convertHistory();
        $this->convertBarcodeSuggestions();
        $this->seedDefaultRolesAndPermissions();
    }

    public function down(): void
    {
        Schema::dropIfExists('product_nutrients');
        Schema::dropIfExists('nutrients');
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('profile_user');
        Schema::dropIfExists('user_notification');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        if (Schema::hasTable('ingredient_ingredient') && ! Schema::hasTable('ingredient_has_ingredients')) {
            Schema::rename('ingredient_ingredient', 'ingredient_has_ingredients');
        }

        if (Schema::hasTable('scan_history_results') && ! Schema::hasTable('history_results')) {
            Schema::rename('scan_history_results', 'history_results');
        }

        if (Schema::hasTable('scans_history') && ! Schema::hasTable('history')) {
            Schema::rename('scans_history', 'history');
        }

        if (Schema::hasTable('barcode_suggestion_scans') && ! Schema::hasTable('barcode_suggestions_confirmations')) {
            Schema::rename('barcode_suggestion_scans', 'barcode_suggestions_confirmations');
        }

        if (Schema::hasTable('subscriptions') && ! Schema::hasTable('user_has_plan')) {
            if (Schema::hasColumn('subscriptions', 'payment_id')) {
                Schema::table('subscriptions', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('payment_id');
                });
            }

            Schema::rename('subscriptions', 'user_has_plan');
        }

        Schema::dropIfExists('payments');

        if (Schema::hasTable('newsletter') && ! Schema::hasTable('newsletter_subscribers')) {
            Schema::rename('newsletter', 'newsletter_subscribers');
        }
    }

    private function createAccessTables(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->tinyIncrements('id');
                $table->string('name', 200)->unique();
            });
        }

        if (! Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 200)->unique();
            });
        }

        if (! Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->unsignedTinyInteger('role_id');
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->primary(['role_id', 'user_id']);
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->unsignedInteger('permission_id');
                $table->unsignedTinyInteger('role_id');
                $table->primary(['permission_id', 'role_id']);
                $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            });
        }
    }

    private function convertPlansAndSubscriptions(): void
    {
        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->decimal('amount', 10, 2)->default(0);
                $table->enum('type', ['trial', 'transaction', 'refund', 'adjustment'])->default('transaction');
                $table->enum('method', ['visa', 'mastercard', 'mercadopago', 'manual', 'unknown'])->default('unknown');
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (! Schema::hasColumn('plans', 'name')) {
                    $table->string('name', 255)->nullable()->after('id');
                }
                if (! Schema::hasColumn('plans', 'price_per_month')) {
                    $table->decimal('price_per_month', 10, 2)->default(0)->after('name');
                }
                if (! Schema::hasColumn('plans', 'price_per_year')) {
                    $table->decimal('price_per_year', 10, 2)->default(0)->after('price_per_month');
                }
            });

            if (Schema::hasColumn('plans', 'plan')) {
                DB::statement('UPDATE plans SET name = plan WHERE name IS NULL OR name = ""');
            }
            if (Schema::hasColumn('plans', 'price')) {
                DB::statement('UPDATE plans SET price_per_month = price WHERE price_per_month = 0');
                DB::statement('UPDATE plans SET price_per_year = price * 12 WHERE price_per_year = 0');
            }

            Schema::table('plans', function (Blueprint $table) {
                if (Schema::hasColumn('plans', 'plan')) {
                    $table->dropColumn('plan');
                }
                if (Schema::hasColumn('plans', 'price')) {
                    $table->dropColumn('price');
                }
                if (Schema::hasColumn('plans', 'duration')) {
                    $table->dropColumn('duration');
                }
            });
        }

        if (Schema::hasTable('user_has_plan') && ! Schema::hasTable('subscriptions')) {
            try {
                Schema::table('user_has_plan', function (Blueprint $table) {
                    $table->dropUnique(['user_id']);
                });
            } catch (Throwable) {
                // Existing installations may have a different generated index name.
            }

            Schema::rename('user_has_plan', 'subscriptions');
        }

        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (! Schema::hasColumn('subscriptions', 'payment_id')) {
                    $table->foreignId('payment_id')->nullable()->after('plan_id')->constrained('payments')->nullOnDelete();
                }
                if (! Schema::hasColumn('subscriptions', 'subscription_start_timestamp')) {
                    $table->timestamp('subscription_start_timestamp')->nullable()->after('payment_id');
                }
                if (! Schema::hasColumn('subscriptions', 'subscription_end_timestamp')) {
                    $table->timestamp('subscription_end_timestamp')->nullable()->after('subscription_start_timestamp');
                }
                if (! Schema::hasColumn('subscriptions', 'auto_renovate')) {
                    $table->boolean('auto_renovate')->default(false)->after('subscription_end_timestamp');
                }
            });

            if (Schema::hasColumn('subscriptions', 'amount')) {
                foreach (DB::table('subscriptions')->where('amount', '>', 0)->whereNull('payment_id')->get() as $subscription) {
                    $paymentId = DB::table('payments')->insertGetId([
                        'user_id' => $subscription->user_id,
                        'amount' => $subscription->amount,
                        'type' => 'transaction',
                        'method' => 'manual',
                        'metadata' => json_encode(['migrated_from' => 'user_has_plan.amount']),
                        'created_at' => $subscription->created_at ?? now(),
                        'updated_at' => $subscription->updated_at ?? now(),
                    ]);

                    DB::table('subscriptions')->where('id', $subscription->id)->update(['payment_id' => $paymentId]);
                }
            }

            if (Schema::hasColumn('subscriptions', 'expires_at')) {
                DB::statement('UPDATE subscriptions SET subscription_end_timestamp = expires_at WHERE subscription_end_timestamp IS NULL');
            }
            DB::statement('UPDATE subscriptions SET subscription_start_timestamp = created_at WHERE subscription_start_timestamp IS NULL');

            Schema::table('subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('subscriptions', 'amount')) {
                    $table->dropColumn('amount');
                }
                if (Schema::hasColumn('subscriptions', 'expires_at')) {
                    $table->dropColumn('expires_at');
                }
            });
        }
    }

    private function convertNewsletter(): void
    {
        if (Schema::hasTable('newsletter_subscribers') && ! Schema::hasTable('newsletter')) {
            Schema::rename('newsletter_subscribers', 'newsletter');
        }

        if (! Schema::hasTable('newsletter')) {
            Schema::create('newsletter', function (Blueprint $table) {
                $table->id();
                $table->string('email', 255)->unique();
                $table->enum('status', ['subscribed', 'unsubscribed'])->default('subscribed');
                $table->timestamp('last_sent_at')->nullable();
                $table->timestamp('subscribed_at')->nullable();
                $table->timestamp('unsubscribed_at')->nullable();
            });

            return;
        }

        Schema::table('newsletter', function (Blueprint $table) {
            if (! Schema::hasColumn('newsletter', 'status')) {
                $table->enum('status', ['subscribed', 'unsubscribed'])->default('subscribed')->after('email');
            }
            if (! Schema::hasColumn('newsletter', 'last_sent_at')) {
                $table->timestamp('last_sent_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('newsletter', 'subscribed_at')) {
                $table->timestamp('subscribed_at')->nullable()->after('last_sent_at');
            }
            if (! Schema::hasColumn('newsletter', 'unsubscribed_at')) {
                $table->timestamp('unsubscribed_at')->nullable()->after('subscribed_at');
            }
        });

        if (Schema::hasColumn('newsletter', 'subscribed')) {
            DB::statement("UPDATE newsletter SET status = CASE WHEN subscribed = 1 THEN 'subscribed' ELSE 'unsubscribed' END");
            Schema::table('newsletter', function (Blueprint $table) {
                $table->dropColumn('subscribed');
            });
        }

        DB::statement('UPDATE newsletter SET subscribed_at = created_at WHERE subscribed_at IS NULL AND status = "subscribed"');
        DB::statement('ALTER TABLE newsletter MODIFY email varchar(255) NOT NULL');
        try {
            Schema::table('newsletter', function (Blueprint $table) {
                $table->unique('email');
            });
        } catch (Throwable) {
            //
        }
    }

    private function createEngagementTables(): void
    {
        if (! Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('title', 255);
                $table->text('body');
                $table->enum('status', ['published', 'resolved'])->default('published');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title', 200);
                $table->text('body');
                $table->timestamp('date')->nullable();
            });
        }

        if (! Schema::hasTable('user_notification')) {
            Schema::create('user_notification', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('notification_id')->constrained()->cascadeOnDelete();
                $table->boolean('is_read')->default(false);
                $table->boolean('is_archived')->default(false);
                $table->primary(['user_id', 'notification_id']);
            });
        }
    }

    private function convertProfiles(): void
    {
        if (Schema::hasTable('profiles')) {
            Schema::table('profiles', function (Blueprint $table) {
                if (! Schema::hasColumn('profiles', 'owner_id')) {
                    $table->foreignId('owner_id')->nullable()->after('avatar')->constrained('users')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('profiles', 'share_token')) {
                    $table->string('share_token', 200)->nullable()->unique()->after('owner_id');
                }
            });

            if (Schema::hasColumn('profiles', 'user_id')) {
                DB::statement('UPDATE profiles SET owner_id = user_id WHERE owner_id IS NULL');
            }
        }

        if (! Schema::hasTable('profile_user')) {
            Schema::create('profile_user', function (Blueprint $table) {
                $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->boolean('can_edit')->default(false);
                $table->primary(['profile_id', 'user_id']);
            });
        }
    }

    private function convertIngredients(): void
    {
        if (Schema::hasTable('ingredients')) {
            Schema::table('ingredients', function (Blueprint $table) {
                if (! Schema::hasColumn('ingredients', 'classified')) {
                    $table->boolean('classified')->default(false)->after('aliases');
                }
                if (! Schema::hasColumn('ingredients', 'is_part_of_main_groups')) {
                    $table->boolean('is_part_of_main_groups')->default(false)->after('classified');
                }
            });
        }

        if (Schema::hasTable('ingredient_has_ingredients') && ! Schema::hasTable('ingredient_ingredient')) {
            Schema::rename('ingredient_has_ingredients', 'ingredient_ingredient');
        }

        if (Schema::hasTable('ingredient_ingredient')) {
            Schema::table('ingredient_ingredient', function (Blueprint $table) {
                if (Schema::hasColumn('ingredient_ingredient', 'belongs_to_id')) {
                    $table->renameColumn('belongs_to_id', 'parent_id');
                }
                if (Schema::hasColumn('ingredient_ingredient', 'owner_id')) {
                    $table->renameColumn('owner_id', 'child_id');
                }
            });
        }

        if (Schema::hasTable('ingredient_profile') && ! Schema::hasColumn('ingredient_profile', 'care')) {
            Schema::table('ingredient_profile', function (Blueprint $table) {
                $table->unsignedTinyInteger('care')->default(0)->comment('0 avoid, 1 warning');
            });
        }

        if (Schema::hasTable('ingredient_product') && ! Schema::hasColumn('ingredient_product', 'is_trace')) {
            Schema::table('ingredient_product', function (Blueprint $table) {
                $table->boolean('is_trace')->default(false)->comment('true when declared as may contain / traces');
            });
        }
    }

    private function convertProducts(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (! Schema::hasColumn('products', 'slug')) {
                    $table->string('slug', 200)->nullable()->unique()->after('img_alt');
                }
                if (! Schema::hasColumn('products', 'embedding')) {
                    $table->json('embedding')->nullable()->after('rnpa');
                }
                if (! Schema::hasColumn('products', 'active')) {
                    $table->boolean('active')->default(true)->after('embedding');
                }
            });
        }

        if (! Schema::hasTable('category_product')) {
            Schema::create('category_product', function (Blueprint $table) {
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->primary(['category_id', 'product_id']);
            });
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'category_id')) {
            DB::statement('
                INSERT IGNORE INTO category_product (category_id, product_id)
                SELECT category_id, id FROM products WHERE category_id IS NOT NULL
            ');
        }

        if (! Schema::hasTable('nutrients')) {
            Schema::create('nutrients', function (Blueprint $table) {
                $table->id();
                $table->string('name', 200);
                $table->string('unit', 200);
                $table->string('category', 200)->nullable();
            });
        }

        if (! Schema::hasTable('product_nutrients')) {
            Schema::create('product_nutrients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('nutrient_id')->constrained()->cascadeOnDelete();
                $table->double('value')->nullable();
                $table->string('per_unit', 30)->default('100g');
                $table->unique(['product_id', 'nutrient_id', 'per_unit']);
            });
        }
    }

    private function convertHistory(): void
    {
        if (Schema::hasTable('history') && ! Schema::hasTable('scans_history')) {
            Schema::rename('history', 'scans_history');
        }

        if (Schema::hasTable('scans_history')) {
            Schema::table('scans_history', function (Blueprint $table) {
                if (! Schema::hasColumn('scans_history', 'scan_img')) {
                    $table->string('scan_img', 255)->nullable()->after('scanned_at');
                }
            });
        }

        if (Schema::hasTable('history_results') && ! Schema::hasTable('scan_history_results')) {
            Schema::rename('history_results', 'scan_history_results');
        }

        if (Schema::hasTable('scan_history_results')) {
            Schema::table('scan_history_results', function (Blueprint $table) {
                if (Schema::hasColumn('scan_history_results', 'history_id')) {
                    $table->renameColumn('history_id', 'scan_history_id');
                }
                if (! Schema::hasColumn('scan_history_results', 'scanned_at')) {
                    $table->timestamp('scanned_at')->nullable()->after('scan_history_id');
                }
                if (! Schema::hasColumn('scan_history_results', 'profile_name')) {
                    $table->string('profile_name', 255)->nullable()->after('scanned_at');
                }
                if (! Schema::hasColumn('scan_history_results', 'profile_avatar')) {
                    $table->string('profile_avatar', 255)->nullable()->after('profile_name');
                }
                if (! Schema::hasColumn('scan_history_results', 'result')) {
                    $table->unsignedTinyInteger('result')->default(3)->after('profile_avatar')->comment('0 safe, 1 warning, 2 unsafe, 3 unknown');
                }
            });

            if (Schema::hasColumn('scan_history_results', 'is_safe')) {
                DB::statement('UPDATE scan_history_results SET result = CASE WHEN is_safe = 1 THEN 0 ELSE 2 END');
            }

            DB::statement('
                UPDATE scan_history_results shr
                LEFT JOIN profiles p ON p.id = shr.profile_id
                LEFT JOIN scans_history sh ON sh.id = shr.scan_history_id
                SET
                    shr.profile_name = COALESCE(shr.profile_name, p.name),
                    shr.profile_avatar = COALESCE(shr.profile_avatar, p.avatar),
                    shr.scanned_at = COALESCE(shr.scanned_at, sh.scanned_at)
            ');

            Schema::table('scan_history_results', function (Blueprint $table) {
                if (Schema::hasColumn('scan_history_results', 'is_safe')) {
                    $table->dropColumn('is_safe');
                }
            });
        }
    }

    private function convertBarcodeSuggestions(): void
    {
        if (Schema::hasTable('barcode_suggestions')) {
            if (Schema::hasColumn('barcode_suggestions', 'status')) {
                DB::statement("ALTER TABLE barcode_suggestions MODIFY status ENUM('pending', 'approved', 'accepted', 'blocked') NOT NULL DEFAULT 'pending'");
                DB::statement("UPDATE barcode_suggestions SET status = 'accepted' WHERE status = 'approved'");
                DB::statement("ALTER TABLE barcode_suggestions MODIFY status ENUM('pending', 'accepted', 'blocked') NOT NULL DEFAULT 'pending'");
            }
        }

        if (Schema::hasTable('barcode_suggestions_confirmations') && ! Schema::hasTable('barcode_suggestion_scans')) {
            Schema::rename('barcode_suggestions_confirmations', 'barcode_suggestion_scans');
        }

        if (Schema::hasTable('barcode_suggestion_scans')) {
            Schema::table('barcode_suggestion_scans', function (Blueprint $table) {
                if (! Schema::hasColumn('barcode_suggestion_scans', 'img')) {
                    $table->string('img', 255)->nullable()->after('user_id');
                }
            });
        }
    }

    private function seedDefaultRolesAndPermissions(): void
    {
        $roles = ['user', 'admin', 'premium'];
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['name' => $role]);
        }

        $permissions = [
            'can_create_profiles',
            'can_use_offline',
            'can_search_products',
            'can_see_all_history',
        ];
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(['name' => $permission]);
        }

        $roleIds = DB::table('roles')->pluck('id', 'name');
        $permissionIds = DB::table('permissions')->pluck('id', 'name');

        $map = [
            'user' => ['can_search_products'],
            'premium' => ['can_create_profiles', 'can_use_offline', 'can_search_products', 'can_see_all_history'],
            'admin' => $permissions,
        ];

        foreach ($map as $role => $rolePermissions) {
            foreach ($rolePermissions as $permission) {
                DB::table('permission_role')->updateOrInsert([
                    'role_id' => $roleIds[$role],
                    'permission_id' => $permissionIds[$permission],
                ]);
            }
        }

        if (Schema::hasColumn('users', 'role')) {
            foreach (DB::table('users')->select('id', 'role')->get() as $user) {
                $role = strtolower($user->role ?: 'user');
                if (! isset($roleIds[$role])) {
                    $role = 'user';
                }

                DB::table('role_user')->updateOrInsert([
                    'user_id' => $user->id,
                    'role_id' => $roleIds[$role],
                ]);
            }
        }
    }
};
