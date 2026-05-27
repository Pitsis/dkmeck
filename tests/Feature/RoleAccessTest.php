<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Filament\Portal\Resources\Tickets\Pages\CreateTicket;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\NewTicketMessageNotification;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $secondAdmin;

    private User $agent;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'agent', 'customer'] as $role) {
            Role::create(['name' => $role]);
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->secondAdmin = User::factory()->create();
        $this->secondAdmin->assignRole('admin');

        $this->agent = User::factory()->create();
        $this->agent->assignRole('agent');

        $this->customer = User::factory()->create();
        $this->customer->assignRole('customer');
    }

    public function test_new_customer_ticket_is_assigned_to_admins_and_emails_them(): void
    {
        Notification::fake();
        Filament::setCurrentPanel('portal');

        Livewire::actingAs($this->customer)
            ->test(CreateTicket::class)
            ->fillForm([
                'subject' => 'Δεν δουλεύει η εκτύπωση',
                'message' => 'Το pdf δεν ανοίγει.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $ticket = Ticket::firstOrFail();

        $this->assertEqualsCanonicalizing(
            [$this->admin->id, $this->secondAdmin->id],
            $ticket->assignees()->pluck('users.id')->all(),
        );

        Notification::assertSentTo([$this->admin, $this->secondAdmin], NewTicketMessageNotification::class);
        Notification::assertNotSentTo($this->customer, NewTicketMessageNotification::class);
    }

    public function test_agent_sees_only_assigned_tickets_in_admin_panel(): void
    {
        $assigned = Ticket::create([
            'user_id' => $this->customer->id,
            'subject' => 'ΑΝΑΤΕΘΕΙΜΕΝΟ_ΣΕ_AGENT',
            'status' => TicketStatus::Open,
        ]);
        $assigned->assignees()->attach($this->agent->id);

        $other = Ticket::create([
            'user_id' => $this->customer->id,
            'subject' => 'ΑΛΛΟ_ΑΙΤΗΜΑ',
            'status' => TicketStatus::Open,
        ]);
        $other->assignees()->attach($this->admin->id);

        $this->actingAs($this->agent)
            ->get('/admin/tickets')
            ->assertOk()
            ->assertSee('ΑΝΑΤΕΘΕΙΜΕΝΟ_ΣΕ_AGENT')
            ->assertDontSee('ΑΛΛΟ_ΑΙΤΗΜΑ');
    }

    public function test_agent_cannot_open_unassigned_ticket(): void
    {
        $other = Ticket::create([
            'user_id' => $this->customer->id,
            'subject' => 'Ξένο',
            'status' => TicketStatus::Open,
        ]);
        $other->assignees()->attach($this->admin->id);

        $this->actingAs($this->agent)
            ->get('/admin/tickets/'.$other->id)
            ->assertNotFound();
    }

    public function test_admin_sees_all_tickets(): void
    {
        $a = Ticket::create(['user_id' => $this->customer->id, 'subject' => 'ΑΛΦΑ', 'status' => TicketStatus::Open]);
        $a->assignees()->attach($this->agent->id);
        Ticket::create(['user_id' => $this->customer->id, 'subject' => 'ΒΗΤΑ', 'status' => TicketStatus::Open]);

        $this->actingAs($this->admin)
            ->get('/admin/tickets')
            ->assertOk()
            ->assertSee('ΑΛΦΑ')
            ->assertSee('ΒΗΤΑ');
    }

    public function test_agent_cannot_access_user_management(): void
    {
        $this->actingAs($this->agent)
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_admin_can_access_user_management(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/users')
            ->assertOk();
    }
}
