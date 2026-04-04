<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(EntrustSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(TagSeeder::class);
        $this->call(ProductsTagsSeeder::class);
        $this->call(ProductsImagesSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(LeadSourceSeeder::class);
        $this->call(LeadStatusSeeder::class);
        $this->call(OpportunitySourceSeeder::class);
        $this->call(LostReasonSeeder::class);
        $this->call(PipelineStageSeeder::class);
        $this->call(LeadStatusSeeder::class);
        $this->call(LeadStatusSeeder::class);
        $this->call(OpportunitySourceSeeder::class);
        // $this->call(PipelineStageSeeder::class);
    }
}
