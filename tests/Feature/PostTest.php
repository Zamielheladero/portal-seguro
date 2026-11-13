<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
| Hallazgo SAST (revisión manual + Larastan): UpdatePostRequest no tenía reglas,
| así que validated() devolvía [] y la edición ni se validaba ni se guardaba.
*/

function publicacionDe(User $autor): Post
{
    return $autor->posts()->create(['title' => 'Horario de diciembre', 'body' => 'Abrimos de 8 a 6.']);
}

test('el autor edita su publicación y el cambio se guarda', function () {
    $autor = User::factory()->create();
    $post = publicacionDe($autor);

    $this->actingAs($autor)
        ->patch("/posts/{$post->id}", ['title' => 'Horario actualizado', 'body' => 'Abrimos de 7 a 7.'])
        ->assertRedirect(route('posts.show', $post));

    $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Horario actualizado']);
});

test('la edición valida los datos igual que la creación', function () {
    $autor = User::factory()->create();
    $post = publicacionDe($autor);

    $this->actingAs($autor)
        ->patch("/posts/{$post->id}", ['title' => '', 'body' => str_repeat('a', 10001)])
        ->assertSessionHasErrors(['title', 'body']);

    $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Horario de diciembre']);
});

test('nadie puede editar ni eliminar la publicación de otra persona', function () {
    $post = publicacionDe(User::factory()->create());
    $otro = User::factory()->create();

    $this->actingAs($otro)->get("/posts/{$post->id}/edit")->assertForbidden();
    $this->actingAs($otro)->patch("/posts/{$post->id}", ['title' => 'Hackeado', 'body' => 'x'])->assertForbidden();
    $this->actingAs($otro)->delete("/posts/{$post->id}")->assertForbidden();

    $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Horario de diciembre']);
});

test('una publicación con HTML se muestra escapada (XSS)', function () {
    $autor = User::factory()->create();
    $post = $autor->posts()->create(['title' => '<script>alert(1)</script>', 'body' => '<b onmouseover=alert(1)>hola</b>']);

    $this->actingAs($autor)->get("/posts/{$post->id}")
        ->assertOk()
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertDontSee('<b onmouseover=alert(1)>', false)
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
});
