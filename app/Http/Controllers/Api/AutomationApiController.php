<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\FormBuilder\Models\FormSubmission;
use Modules\Analytics\Models\AnalyticsClickEvent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AutomationApiController extends Controller
{
    /**
     * Get recent form submissions (leads).
     * Ideal for n8n or other CRM integrations.
     */
    public function getLeads(Request $request)
    {
        $limit = $request->get('limit', 50);
        $formKey = $request->get('form_key');

        $query = FormSubmission::latest();

        if ($formKey) {
            $query->where('form_key', $formKey);
        }

        $leads = $query->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'total' => $leads->total(),
            ]
        ]);
    }

    /**
     * Push a lead from an external source.
     */
    public function pushLead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_key' => 'required|string',
            'payload' => 'required|array',
            'page_slug' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $submission = FormSubmission::create([
            'form_key' => $request->form_key,
            'page_slug' => $request->page_slug ?? 'external-api',
            'payload' => $request->payload,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ?? 'Automation-Agent',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Lead pushed successfully',
            'data' => $submission
        ], 201);
    }

    /**
     * Trigger/Track a WhatsApp interaction from an external automation.
     */
    public function trackWaTrigger(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'text' => 'nullable|string',
            'source' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Logic similar to WhatsAppController@redirect but for server-side tracking
        $event = AnalyticsClickEvent::create([
            'session_id' => $request->get('session_id', (string) Str::uuid()),
            'event_type' => 'whatsapp',
            'event_label' => $request->get('source', 'Automation Trigger'),
            'ip_address' => $request->ip(),
            'user_agent' => 'Automation-Agent',
            'target_value' => $request->phone,
            'meta' => [
                'message' => $request->text,
                'external_trigger' => true
            ],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'WhatsApp interaction tracked',
            'event_id' => $event->id
        ]);
    }
}
