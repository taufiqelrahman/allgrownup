<?php

use Illuminate\Database\Seeder;

class StoryPagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('story_pages')->insert([
            'filepath' => 'images/1573976854_front_right.jpeg',
            'child_gender' => 'boy',
            'child_type' => 'light',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        
        DB::table('story_pages')->insert([
            'filepath' => 'images/1573976965_front_left.jpeg',
            'child_gender' => 'boy',
            'child_type' => 'light',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        
        DB::table('story_pages')->insert([
            'filepath' => 'images/1573977140_dedication.jpeg',
            'child_gender' => 'boy',
            'child_type' => 'light',
            'dedication' => true,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        
        DB::table('story_pages')->insert([
            'filepath' => 'images/1573977226_farmyard_left.jpeg',
            'child_gender' => 'boy',
            'child_type' => 'light',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        
        DB::table('story_pages')->insert([
            'filepath' => 'images/1573977274_farmyard_right.jpeg',
            'child_gender' => 'boy',
            'child_type' => 'light',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
    }
}
