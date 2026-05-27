{{--
    Tool Data JSON Partial
    Generates the JSON string for data-tool attribute.
    
    Required: $tool (Tool model or array with name, desc, url, cat, type, logo, compatibility)
    Optional: $isModel (default true) — set to false if $tool is an array
--}}
@php
    if (!isset($isModel)) $isModel = is_object($tool);

    if ($isModel) {
        $toolData = [
            'id' => $tool->id,
            'name' => $tool->name,
            'desc' => $tool->description,
            'url' => $tool->url,
            'cat' => $tool->categoryRelation?->name,
            'type' => $tool->is_google_workspace ? 'Google Workspace' : '3rd Party',
            'logo' => $tool->logo_url ? asset($tool->logo_url) : null,
            'compatibility' => $tool->compatibility ?? null,
            'clicks' => $tool->click_count ?? 0,
        ];
    } else {
        $toolData = $tool;
    }
@endphp
{!! json_encode($toolData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
