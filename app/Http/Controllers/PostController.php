<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Récupérer la liste des posts",
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des posts récupérée avec succès",
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Post::all(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Créer un nouveau post",
     *     tags={"Posts"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "body"},
     *             @OA\Property(property="title", type="string", example="Mon premier post"),
     *             @OA\Property(property="body", type="string", example="Ceci est le contenu de mon post.")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Post créé avec succès"),
     *     @OA\Response(response=400, description="Données invalides")
     * )
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        $post = $request->user()->posts()->create($fields);

        return response()->json(['post' => $post, 'message' => 'Post créé avec succès'], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Afficher un post spécifique",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Post trouvé"),
     *     @OA\Response(response=404, description="Post non trouvé")
     * )
     */
    public function show(Post $post)
    {
        return response()->json($post, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Mettre à jour un post",
     *     tags={"Posts"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du post à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "body"},
     *             @OA\Property(property="title", type="string", example="Titre mis à jour"),
     *             @OA\Property(property="body", type="string", example="Nouveau contenu du post.")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Post mis à jour"),
     *     @OA\Response(response=403, description="Non autorisé"),
     *     @OA\Response(response=404, description="Post non trouvé")
     * )
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('modify', $post);

        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        $post->update($fields);

        return response()->json(['post' => $post, 'message' => 'Post mis à jour avec succès'], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Supprimer un post",
     *     tags={"Posts"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du post à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Post supprimé avec succès"),
     *     @OA\Response(response=403, description="Non autorisé"),
     *     @OA\Response(response=404, description="Post non trouvé")
     * )
     */
    public function destroy(Post $post)
    {
        Gate::authorize('modify', $post);
        $post->delete();

        return response()->json(['message' => 'Le post a été supprimé avec succès'], 200);
    }
}
