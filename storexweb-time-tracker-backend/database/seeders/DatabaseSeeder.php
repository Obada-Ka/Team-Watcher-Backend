<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
// use App\Models\Permission;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $dev_role = new Role();
		$dev_role->slug = 'admin';
		$dev_role->name = 'admin';
		$dev_role->save();

		$admin = new Role();
		$admin->slug = 'user';
		$admin->name = 'user';
		$admin->save();

		$dev_role = Role::where('slug','user')->first();
		$admin = Role::where('slug', 'admin')->first();

		$dev_role = Role::where('slug','user')->first();
		$admin = Role::where('slug', 'admin')->first();
		$developer = new User();
		$developer->name = 'marea';
		$developer->email = 'marea@gmail.com';
		$developer->password = bcrypt('123456');
		$developer->save();
		$developer->roles()->attach($dev_role);

		$manager = new User();
		$manager->name = 'obada';
		$manager->email = 'obada@gmail.com';
		$manager->password = bcrypt('123456');
		$manager->save();
		$manager->roles()->attach($admin);

    }
}
