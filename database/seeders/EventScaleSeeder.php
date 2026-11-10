<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventScaleSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();
        $now = now();

        // 20 extra venues
        $rows = [];
        for ($i = 1; $i <= 20; $i++) {
            $rows[] = ['name' => "Venue $i", 'address' => "Block $i", 'capacity' => rand(50, 1000),
                       'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('venues')->insert($rows);
        $venueIds = DB::table('venues')->pluck('id')->all();

        // 200 participants
        $rows = [];
        for ($i = 1; $i <= 200; $i++) {
            $rows[] = ['name' => fake()->name(), 'email' => "student$i@example.com",
                       'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('participants')->insert($rows);
        $participantIds = DB::table('participants')->pluck('id')->all();

        // building blocks for descriptions ("zebra" is NOT in any of these)
        $openers = ['Annual', 'Student-led', 'Department', 'Open-campus', 'Community'];
        $middles = ['workshop on robotics', 'career networking night', 'music and arts showcase',
                    'sports tournament', 'coding bootcamp', 'research poster session', 'charity fun run'];
        $closers = ['with free snacks', 'open to all year levels', 'bring your student ID',
                    'limited slots available', 'sponsored by local partners'];
        $dressCodes = ['casual', 'formal', 'smart casual', 'uniform', 'costume'];
        $tags = ['tech', 'music', 'sports', 'career', 'arts', 'food', 'charity'];
        $themes = ['tech', 'sports', 'arts', 'career', 'community'];

        // 20,000 events, inserted 1,000 rows at a time
        foreach (array_chunk(range(1, 20000), 1000) as $chunk) {
            $rows = [];
            foreach ($chunk as $n) {
                $description = $openers[array_rand($openers)] . ' ' .
                               $middles[array_rand($middles)] . ' ' .
                               $closers[array_rand($closers)];
                $details = [
                    'dress_code' => $dressCodes[array_rand($dressCodes)],
                    'capacity'   => rand(30, 500),
                    'tags'       => array_rand(array_flip($tags), 2),
                    'theme'      => $themes[array_rand($themes)],
                ];
                // rare value: 100 of 20,000 events = 0.5% (under 1%)
                if ($n % 200 === 0) {
                    $description .= ' featuring a zebra mascot';
                    $details['theme'] = 'zebra';
                }
                $rows[] = [
                    'venue_id'    => $venueIds[array_rand($venueIds)],
                    'title'       => "Event $n",
                    'description' => $description,
                    'details'     => json_encode($details),
                    'starts_at'   => now()->addDays(rand(-60, 120))->setTime(rand(7, 18), 0),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
            DB::table('events')->insert($rows);
        }

        // registrations for the first 150 events
        $eventIds = DB::table('events')->orderBy('id')->limit(150)->pluck('id');
        $rows = [];
        foreach ($eventIds as $eventId) {
            foreach (array_rand(array_flip($participantIds), rand(3, 10)) as $pid) {
                $rows[] = ['event_id' => $eventId, 'participant_id' => $pid, 'status' => 'registered',
                           'created_at' => $now, 'updated_at' => $now];
            }
        }
        DB::table('event_participant')->insertOrIgnore($rows);
    }
}