<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganisationTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCannotSeeOtherOrganisationData()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $organisation = Organisation::factory()->create();

        OrganisationUser::create(['userId' => $anotherUser->userId, 'orgId' => $organisation->orgId]);

        $this->actingAs($user);

        $response = $this->getJson('/api/organisations');

        $response->assertStatus(200);
        $response->assertJsonMissing(['orgId' => $organisation->orgId]);
    }
}
