<?php

namespace App\Http\Controllers;

use App\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $companies = Company::orderBy('id', 'DESC')->paginate(15);

        return view('admin.companies.index', compact('companies'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'title' => 'required',
            'address' => 'required',
            'description' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'bailee' => 'required',
            'logo_url' => 'required',
        ]);


        $company = Company::create([
            'title' => $request->get('title') ?? '',
            'address' => $request->get('address') ?? '',
            'description' => $request->get('description') ?? '',
            'phone' => $request->get('phone') ?? '',
            'email' => $request->get('email') ?? '',
            'bailee' => $request->get('bailee') ?? '',
            'logo_url' => $request->get('logo_url') ?? '',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('companies.index')
            ->with('success', 'Компания успешно добавлена');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $company = Company::find($id);

        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $company = Company::find($id);

        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'address' => 'required',
            'description' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'bailee' => 'required',
            'logo_url' => 'required',
        ]);


        $company = Company::find($id);
        $company->title = $request->get('title') ?? '';
        $company->address = $request->get('address') ?? '';
        $company->description = $request->get('description') ?? '';
        $company->phone = $request->get('phone') ?? '';
        $company->email = $request->get('email') ?? '';
        $company->bailee = $request->get('bailee') ?? '';
        $company->logo_url = $request->get('logo_url') ?? '';
        $company->save();

        return redirect()
            ->route('companies.show', $id)
            ->with('success', 'Компания успешно отредактирована');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $company = Company::find($id);
        $company->delete();
        return redirect()
            ->route('companies.index')
            ->with('success', 'Компания успешно удалена');
    }
}
