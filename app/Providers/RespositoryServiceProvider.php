<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\{
    UserContract,
    DesignContract,
    CommentContract,
    TeamContract,
    InvitationContract,
    ChatContract,
    MessageContract,
};
use App\Repositories\Eloquent\{
    UserRepository,
    DesignRepository,
    CommentRepository,
    TeamRepository,
    InvitationRepository,
    ChatRepository,
    MessageRepository,
};

class RespositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(UserContract::class, UserRepository::class);
        $this->app->bind(DesignContract::class, DesignRepository::class);
        $this->app->bind(CommentContract::class, CommentRepository::class);
        $this->app->bind(TeamContract::class, TeamRepository::class);
        $this->app->bind(InvitationContract::class, InvitationRepository::class);
        $this->app->bind(ChatContract::class, ChatRepository::class);
        $this->app->bind(MessageContract::class, MessageRepository::class);
    }
}
