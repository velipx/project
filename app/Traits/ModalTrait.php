<?php

namespace App\Traits;

use Inertia\Inertia;
use Illuminate\Http\Request;

trait ModalTrait
{
    /**
     * Prikaz modalnog prozora za kreiranje.
     *
     * @param string $modalName
     * @param string $baseRoute
     * @return \Inertia\Response
     */
    public function showCreateModal(string $modalName, string $baseRoute)
    {
        return Inertia::modal($modalName, [])
            ->baseRoute($baseRoute)
            ->refreshBackdrop();
    }

    /**
     * Prikaz modalnog prozora za potvrdu brisanja.
     *
     * @param Request $request
     * @param string $modalName
     * @param string $deleteRoute
     * @param string $baseRoute
     * @param string $modelClass
     * @return \Inertia\Response
     */
    public function showDeleteConfirmationModal(Request $request, string $modalName, string $deleteRoute, string $baseRoute, string $modelClass)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:' . strtolower(class_basename($modelClass)) . ',id',
        ]);

        $items = $modelClass::withTrashed()->whereIn('id', $validated['ids'])->get();

        return Inertia::modal($modalName, [
            'resources' => $items,
            'deleteRoute' => $deleteRoute,
        ])->baseRoute($baseRoute)
            ->refreshBackdrop();
    }

    /**
     * Vrati resurs iz `trashed` stanja.
     *
     * @param int $id
     * @param string $modelClass
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restoreResource(int $id, string $modelClass)
    {
        $resource = $modelClass::withTrashed()->find($id);

        if ($resource) {
            $resource->restore();
            return redirect()->route(strtolower(class_basename($modelClass)) . 's.index')
                ->with('success', 'Resource restored successfully.');
        }

        return redirect()->route(strtolower(class_basename($modelClass)) . 's.index')
            ->with('error', 'Resource not found.');
    }

    /**
     * Ukloni resurse iz `storage`.
     *
     * @param Request $request
     * @param string $modelClass
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyResources(Request $request, string $modelClass)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:' . strtolower(class_basename($modelClass)) . ',id',
        ]);

        $trashedItems = $modelClass::onlyTrashed()->whereIn('id', $validated['ids'])->get();

        foreach ($trashedItems as $item) {
            $item->forceDelete();
        }

        $modelClass::whereIn('id', $validated['ids'])->delete();

        return redirect()->route(strtolower(class_basename($modelClass)) . 's.index')
            ->with('success', 'Selected resources have been deleted successfully.');
    }
}
