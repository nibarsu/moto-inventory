<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCompanySettingRequest;
use App\Models\CompanySetting;

class CompanySettingController extends Controller
{
    public function edit()
    {
        return view('company-settings.edit', [
            'companySetting' => CompanySetting::current(),
        ]);
    }

    public function update(UpdateCompanySettingRequest $request)
    {
        CompanySetting::query()->updateOrCreate(
            ['id' => 1],
            $request->validated()
        );

        return redirect()
            ->route('company-settings.edit')
            ->with('success', '商家名稱已更新。');
    }
}
