<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Comment;
use App\Models\Genre;
use App\Models\Like;
use App\Models\Movie;
use App\Models\Notification;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create();
        $fromUser = User::factory()->create();
        $genres = Genre::pluck('id');

        $movie = Movie::factory()->create(['user_id' => $user->id]);

        $movie->genres()->sync($genres);

        $quotes = Quote::factory(3)->create(['movie_id' => $movie->id]);

        foreach ($quotes as $quote) {
            $comments = Comment::factory(1)->create(['quote_id' => $quote->id, 'user_id' => $fromUser->id]);
            $likes = Like::factory(1)->create(['user_id' => $fromUser->id, 'quote_id' => $quote->id]);

            foreach ($comments as $comment) {
                Notification::factory()->create([
                    'quote_id' => $comment->quote_id,
                    'from_user' => $comment->user_id,
                    'to_user' => $user->id,
                    'notification' => 'comment'
                ]);
            }

            foreach ($likes as $like) {
                Notification::factory()->create([
                    'quote_id' => $like->quote_id,
                    'from_user' => $like->user_id,
                    'to_user' => $user->id,
                    'notification' => 'like'
                ]);
            }
        }
    }
}
