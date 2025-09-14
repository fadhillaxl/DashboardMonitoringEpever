<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Display a listing of sites.
     */
    public function index()
    {
        $sites = Site::all();
        return view('sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new site.
     */
    public function create()
    {
        return view('sites.create');
    }

    /**
     * Store a newly created site in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_site' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'lokasi' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'mac_address' => 'required|string|max:255|unique:sites,mac_address',
        ]);

        Site::create($validated);

        return redirect()->route('sites.index')->with('success', 'Site created successfully.');
    }

    /**
     * Display the specified site by MAC address.
     */
    public function show($mac_address)
    {
        $site = Site::where('mac_address', $mac_address)->firstOrFail();
        return view('sites.show', compact('site'));
    }

    /**
     * Show the form for editing the specified site by MAC address.
     */
    public function edit($mac_address)
    {
        $site = Site::where('mac_address', $mac_address)->firstOrFail();
        return view('sites.edit', compact('site'));
    }

    /**
     * Update the specified site in storage by MAC address.
     */
    public function update(Request $request, $mac_address)
    {
        $site = Site::where('mac_address', $mac_address)->firstOrFail();

        $validated = $request->validate([
            'nama_site' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'lokasi' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'mac_address' => 'required|string|max:255|unique:sites,mac_address,' . $site->id,
        ]);

        $site->update($validated);

        return redirect()->route('sites.index')->with('success', 'Site updated successfully.');
    }

    /**
     * Remove the specified site from storage by MAC address.
     */
    public function destroy($mac_address)
    {
        $site = Site::where('mac_address', $mac_address)->firstOrFail();
        $site->delete();

        return redirect()->route('sites.index')->with('success', 'Site deleted successfully.');
    }
}
