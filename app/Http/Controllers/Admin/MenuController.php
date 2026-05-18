<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class MenuController extends Controller
{
    /**
     * Display a listing of menus.
     */
    public function index(): Response
    {
        $menus = Menu::with('roles')
            ->orderBy('order')
            ->get()
            ->map(fn (Menu $menu) => [
                'id' => $menu->id,
                'title' => $menu->title,
                'url' => $menu->url,
                'icon' => $menu->icon,
                'order' => $menu->order,
                'is_active' => $menu->is_active,
                'roles' => $menu->roles->pluck('name'),
                'created_at' => $menu->created_at->toISOString(),
            ]);

        return Inertia::render('admin/Menus', [
            'menus' => $menus,
            'roles' => Role::all()->pluck('name'),
        ]);
    }

    /**
     * Store a newly created menu.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'icon' => ['required', 'string', 'max:100'],
            'order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $menu = Menu::create([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'icon' => $validated['icon'] ?? 'Circle',
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (! empty($validated['roles'])) {
            $roleIds = Role::whereIn('name', $validated['roles'])->pluck('id');
            $menu->roles()->sync($roleIds);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Menu created.')]);

        return to_route('admin.menus.index');
    }

    /**
     * Update the specified menu.
     */
    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'icon' => ['required', 'string', 'max:100'],
            'order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $menu->update([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'icon' => $validated['icon'],
            'order' => $validated['order'] ?? $menu->order,
            'is_active' => $validated['is_active'] ?? $menu->is_active,
        ]);

        $roleIds = Role::whereIn('name', $validated['roles'] ?? [])->pluck('id');
        $menu->roles()->sync($roleIds);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Menu updated.')]);

        return to_route('admin.menus.index');
    }

    /**
     * Remove the specified menu.
     */
    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->roles()->detach();
        $menu->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Menu deleted.')]);

        return to_route('admin.menus.index');
    }

    /**
     * Reorder menus.
     */
    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:menus,id'],
        ]);

        foreach ($validated['ids'] as $index => $id) {
            Menu::where('id', $id)->update(['order' => $index]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Menu order updated.')]);

        return back();
    }
}
