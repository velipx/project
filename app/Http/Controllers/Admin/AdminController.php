<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

abstract class AdminController extends Controller
{
    protected const CREATE_MODAL_VIEW = ''; // Default value
    protected const DELETE_MODAL_VIEW = ''; // Default value
    protected const BASE_ROUTE = ''; // Default value

    /**
     * Get the model class.
     *
     * @return string
     */
    abstract protected function getModel(): string;

    /**
     * Get the model table name.
     *
     * @return string
     */
    abstract protected function getModelTable(): string;

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function showCreateModal()
    {
        return Inertia::modal(static::CREATE_MODAL_VIEW, [])
            ->baseRoute(static::BASE_ROUTE)
            ->refreshBackdrop();
    }

    /**
     * Show the form for deleting resources.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function showDeleteConfirmationModal(Request $request)
    {
        $this->validateDeleteRequest($request);

        $resources = $this->getModel()::withTrashed()->whereIn('id', $request->ids)->get();

        return Inertia::modal(static::DELETE_MODAL_VIEW, [
            'resources' => $resources,
            'deleteRoute' => route(static::BASE_ROUTE . '.destroy'),
        ])->baseRoute(static::BASE_ROUTE)
            ->refreshBackdrop();
    }

    /**
     * Validate delete request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function validateDeleteRequest(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:' . $this->getModelTable() . ',id',
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(int $id): \Illuminate\Http\RedirectResponse
    {
        $resource = $this->getModel()::withTrashed()->find($id);

        if ($resource) {
            $resource->restore();
            return redirect()->route(static::BASE_ROUTE)
                ->with('success', 'Resource restored successfully.');
        }

        return redirect()->route(static::BASE_ROUTE)
            ->with('error', 'Resource not found.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->validateDeleteRequest($request);

        $selectedIds = $request->ids;
        $trashedResources = $this->getModel()::onlyTrashed()->whereIn('id', $selectedIds)->get();

        foreach ($trashedResources as $resource) {
            $resource->forceDelete();
        }

        $this->getModel()::whereIn('id', $selectedIds)->delete();

        return redirect()->route(static::BASE_ROUTE)
            ->with('success', 'Selected resources have been deleted successfully.');
    }
}
