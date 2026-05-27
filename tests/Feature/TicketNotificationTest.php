<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Filament\Portal\Auth\Register;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\NewTicketMessageNotification;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TicketNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private User $adminOne;

    private User $adminTwo;

    private Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'customer']);

        $this->customer = User::factory()->create();
        $this->customer->assignRole('customer');

        $this->adminOne = User::factory()->create();
        $this->adminOne->assignRole('admin');

        $this->adminTwo = User::factory()->create();
        $this->adminTwo->assignRole('admin');

        $this->ticket = Ticket::create([
            'user_id' => $this->customer->id,
            'subject' => 'Δοκιμαστικό αίτημα',
            'status' => TicketStatus::Open,
        ]);

        $this->ticket->assignees()->attach([$this->adminOne->id, $this->adminTwo->id]);
    }

    public function test_customer_reply_notifies_all_assigned_admins(): void
    {
        Notification::fake();

        $this->ticket->messages()->create([
            'user_id' => $this->customer->id,
            'body' => 'Χρειάζομαι βοήθεια.',
        ]);

        Notification::assertSentTo([$this->adminOne, $this->adminTwo], NewTicketMessageNotification::class);
        Notification::assertNotSentTo($this->customer, NewTicketMessageNotification::class);
    }

    public function test_admin_reply_notifies_the_customer_only(): void
    {
        Notification::fake();

        $this->ticket->messages()->create([
            'user_id' => $this->adminOne->id,
            'body' => 'Σας απαντάμε άμεσα.',
        ]);

        Notification::assertSentTo($this->customer, NewTicketMessageNotification::class);
        Notification::assertNotSentTo([$this->adminOne, $this->adminTwo], NewTicketMessageNotification::class);
    }

    public function test_admin_can_view_ticket_conversation_page(): void
    {
        $this->ticket->messages()->create([
            'user_id' => $this->customer->id,
            'body' => 'ΜΟΝΑΔΙΚΟ_ΜΗΝΥΜΑ_ΠΕΛΑΤΗ',
        ]);

        $this->actingAs($this->adminOne)
            ->get('/admin/tickets/'.$this->ticket->id)
            ->assertOk()
            ->assertSee('ΜΟΝΑΔΙΚΟ_ΜΗΝΥΜΑ_ΠΕΛΑΤΗ');
    }

    public function test_customer_can_view_their_own_ticket(): void
    {
        $this->ticket->messages()->create([
            'user_id' => $this->customer->id,
            'body' => 'ΔΙΚΟ_ΜΟΥ_ΑΙΤΗΜΑ',
        ]);

        $this->actingAs($this->customer)
            ->get('/tickets/'.$this->ticket->id)
            ->assertOk()
            ->assertSee('ΔΙΚΟ_ΜΟΥ_ΑΙΤΗΜΑ');
    }

    public function test_customer_cannot_view_another_customers_ticket(): void
    {
        $otherCustomer = User::factory()->create();
        $otherCustomer->assignRole('customer');

        $otherTicket = Ticket::create([
            'user_id' => $otherCustomer->id,
            'subject' => 'Ξένο αίτημα',
            'status' => TicketStatus::Open,
        ]);

        $this->actingAs($this->customer)
            ->get('/tickets/'.$otherTicket->id)
            ->assertNotFound();
    }

    public function test_registration_assigns_customer_role(): void
    {
        Filament::setCurrentPanel('portal');

        Livewire::test(Register::class)
            ->fillForm([
                'name' => 'Νέος Πελάτης',
                'email' => 'newcustomer@example.com',
                'password' => 'password',
                'passwordConfirmation' => 'password',
            ])
            ->call('register')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'newcustomer@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('customer'));
    }

    public function test_customer_reply_sets_status_to_open_and_admin_reply_sets_answered(): void
    {
        $this->ticket->update(['status' => TicketStatus::Answered]);
        $this->ticket->messages()->create([
            'user_id' => $this->customer->id,
            'body' => 'Ακόμη έχω θέμα.',
        ]);
        $this->assertSame(TicketStatus::Open, $this->ticket->fresh()->status);

        $this->ticket->messages()->create([
            'user_id' => $this->adminOne->id,
            'body' => 'Το κοιτάμε.',
        ]);
        $this->assertSame(TicketStatus::Answered, $this->ticket->fresh()->status);
    }
}
