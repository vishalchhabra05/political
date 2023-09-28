<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\AdminUser;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormFieldOption;
use App\Models\PoliticalParty;
use App\Models\MemberExtraInfo;
use App\Models\SurveyFeedback;
use App\Models\Survey;
use Helpers;
use Hash;
use DB;
use Log;
use \Config;

class FormCustomizationController extends Controller
{

    public function index($formType,$formId=null){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            // Form id will come only in case of "Survey" form type
            $formRelatedSurvey = "";
            $politicalParties = [];
            if(!empty($formId)){
                $formRelatedSurvey = Survey::where('form_id', $formId)->where('PPID', $checkLoginAsParty)->first();
                if(empty($formRelatedSurvey)){
                    return redirect()->back()->with('error',UNAUTHORIZED_ACCESS);
                }
                $formRelatedSurvey = $formRelatedSurvey->survey_name;
            }else{
                $politicalParties = PoliticalParty::where('status', 1)->pluck('party_name', 'id');
            }
            return view('commonadmin.form_customize_manage.list', compact('formType', 'formId', 'formRelatedSurvey', 'politicalParties'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id','political_party', 'form_type', 'field_name', 'field_type', 'status', 'created_at', 'action'];
        $reqFormType = $request->formType;
        $reqFormId = null;
        if($reqFormType == 'survey'){
            if(empty($request->formId)){
                $json_data = array(
                    "draw" => 0,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => []
                );
                echo json_encode($json_data);
            }
            $reqFormId = $request->formId;
        }
        $totalData = FormField::whereHas('formInfo', function ($query1) use ($reqFormType) {
            $query1->where('form_type', $reqFormType);
        });

        if(!empty($checkLoginAsParty)){
            $totalData = $totalData->where('PPID', $checkLoginAsParty);
        }
        if(!empty($reqFormId)){
            $totalData = $totalData->where('form_id', $reqFormId);
        }
        $totalData = $totalData->where('status', '!=', 2)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = FormField::whereHas('formInfo', function ($query1) use ($reqFormType) {
            $query1->where('form_type', $reqFormType);
        })
        ->with(['politicalPartyInfo' => function ($query) {
            $query->select('id', 'party_name');
        }])
        ->with(['formInfo' => function ($query) {
            $query->select('id', 'form_type');
        }])->where('status', '!=', 2);

        if(!empty($checkLoginAsParty)){
            $results = $results->where('PPID', $checkLoginAsParty);
        }
        if(!empty($reqFormId)){
            $results = $results->where('form_id', $reqFormId);
        }

        if(!empty($request->political_party)){
            $results = $results->where('PPID', $request->political_party);
        }

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->whereHas('politicalPartyInfo', function ($query1) use ($search) {
                    $query1->where('party_name', 'LIKE', "%{$search}%");
                })
                ->orWhere('field_name', 'LIKE', "%{$search}%")
                ->orWhere('field_type', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['political_party'] = $row->politicalPartyInfo->party_name;
                $nestedData['form_type'] = $row->formInfo->form_type;
                $nestedData['field_name'] = $row->field_name;
                $nestedData['field_type'] = getFieldTypes($row->field_type);
                $nestedData['status'] = getStatus($row->status,$row->id);
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                $nestedData['action'] = "<a href='".route('show_form_customization',['id' => $row->id, 'formType' => $reqFormType, 'formId' => $reqFormId])."' title='View'><button type='button' class='icon-btn view'><i class='fal fa-eye'></i></button></a>&nbsp;";
                if($row->status == 1){
                    $nestedData['action'] = $nestedData['action']."<a href='".route('edit_form_customization',['id' => $row->id, 'formType' => $reqFormType, 'formId' => $reqFormId])."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
                }
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }

    public function create($formType,$formId=null){
        try{
            $entity = new Form;
            $politicalParties = [];
            if(empty($formId)){
                $politicalParties = PoliticalParty::where('status', 1)->pluck('party_name', 'id');
            }else{
                $formDetail = Form::where('id', $formId)->first();
                $politicalPartyDet = PoliticalParty::where('id', $formDetail->PPID)->first();
                $entity->political_party = $politicalPartyDet->id;
                $entity->politicalPartyName = $politicalPartyDet->party_name;
            }

            $fieldTypes = getFieldTypes();
            $isRequiredTypes = getIsRequired();
            $customFieldTabs = getCustomFieldTabs();
            return view('commonadmin.form_customize_manage.add',compact('entity', 'politicalParties', 'fieldTypes', 'isRequiredTypes', 'customFieldTabs', 'formType', 'formId'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function store(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $getFieldTypesString = getFieldTypesString();
            $getFormTypesString = getFormTypesString();
            $validator = Validator::make($request->all(), [
                'form_type' => 'required|in:'.$getFormTypesString,
                'field_name' => 'required|max:50',
                'es_field_name' => 'required|max:50',
                'is_required' => 'required',
                'field_type' => 'required|in:'.$getFieldTypesString,
                'field_min_length' => 'nullable|numeric',
                'field_max_length' => 'nullable|numeric|gt:field_min_length',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                $request->political_party = $checkLoginAsParty;

                // start a transaction
                DB::beginTransaction();

                // Check if political party with same form type exists or not
                $reqFormId = null;
                if(empty($request->formId)){
                    $checkPPForm = Form::where('PPID', $request->political_party)->where('form_type', $request->form_type)->first();
                }else{
                    $reqFormId = $request->formId;
                    $checkPPForm = Form::where('id', $reqFormId)->first();
                }

                if(!empty($checkPPForm)){
                    $formId = $checkPPForm->id;
                    $checkField = FormField::where('form_id', $checkPPForm->id)->where('field_name', $request->field_name)->first();
                    if(!empty($checkField)){
                        $errorObj = (object) [];
                        $errorObj->field_name = ["Field under this political party form already exist."];
                        return redirect()->back()->withInput()->withErrors($errorObj);
                    }
                }else{
                    // Create Political Party Form Type For custom fields
                    $createForm = Form::create([
                        'PPID' => $request->political_party,
                        'form_type' => $request->form_type,
                    ]);
                    $formId = $createForm->id;
                }

                // Create Form Field Data
                $createFormFieldData = [
                    'PPID' => $request->political_party,
                    'form_id' => $formId,
                    'field_name'=> $request->field_name,
                    'es_field_name'=> $request->es_field_name,
                    'tab_type'=> (!empty($request->tab_type)?$request->tab_type:NULL),
                    'is_required'=> ($request->is_required == 'true'?1:0),
                    'field_type'=> $request->field_type,
                ];

                if(in_array($request->field_type, ['text','number','textarea'])){
                    $createFormFieldData['field_min_length'] = (!empty($request->field_min_length)?$request->field_min_length:NULL);
                    $createFormFieldData['field_max_length'] = (!empty($request->field_max_length)?$request->field_max_length:NULL);
                    if($request->field_type == 'text'){
                        $createFormFieldData['decimal_points'] = (!empty($request->decimal_points)?$request->decimal_points:NULL);
                    }

                    // Create Form Field
                    $createFormField = FormField::create($createFormFieldData);
                }elseif(in_array($request->field_type, ['checkbox','radio','dropdown'])){
                    // Create Form Field
                    $createFormField = FormField::create($createFormFieldData);

                    $splitFieldOptions = explode(',', ($request->field_options));

                    if(empty($splitFieldOptions) || count($splitFieldOptions) == 0){
                        $errorObj = (object) [];
                        $errorObj->field_options = ["Field options must be in correct format."];
                        return redirect()->back()->withInput()->withErrors($errorObj);
                    }
                    // trim whitespace and apply the camel case function to each element of the array
                    $splitFieldOptions = array_map(function($item) {
                        return toCamelCase(trim($item));
                    }, $splitFieldOptions);

                    if(count(array_unique($splitFieldOptions)) < count($splitFieldOptions)){
                        $errorObj = (object) [];
                        $errorObj->field_options = ["Field options should not be same."];
                        return redirect()->back()->withInput()->withErrors($errorObj);
                    }else{
                        // Create Form Field Option
                        foreach($splitFieldOptions as $key => $optionVal){
                            $createFormFieldOption = FormFieldOption::create([
                                'PPID' => $request->political_party,
                                'form_field_id' => $createFormField->id,
                                'option'=> $optionVal,
                            ]);
                        }
                    }
                }

                // commit the transaction
                DB::commit();
                return redirect()->route('list_form_customization',['formType' => $request->form_type, 'formId' => $reqFormId])->with('success','Political Party Form Field Created Successfully.');
            }
        }catch(\Exception $e){
            // rollback the transaction in case of an error
            DB::rollback();
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function show($id, $formType, $formId=null){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            // Can see detail of particular political through which user is loggedin
            $entity = FormField::where('id', $id)->where('PPID', $checkLoginAsParty)
            ->with(['politicalPartyInfo' => function ($query) {
                $query->select('id', 'party_name');
            }])
            ->with(['formInfo' => function ($query) {
                $query->select('id', 'form_type');
            }])
            ->with(['formFieldOptionInfo' => function ($query) {
                $query->select('id', 'form_field_id', 'option');
            }])
            ->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }

            if(!empty($entity->formFieldOptionInfo) && count($entity->formFieldOptionInfo) > 0){
                $optionArr = [];
                foreach($entity->formFieldOptionInfo as $key => $optionVal){
                    $optionArr[] = $optionVal->option;
                }
                $entity->formFieldOptionInfo = implode(', ', $optionArr);
            }else{
                $entity->formFieldOptionInfo = "";
            }

            return view('commonadmin.form_customize_manage.show', compact('entity', 'formType', 'formId'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function edit($id, $formType, $formId=null){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            // Can see detail of particular political through which user is loggedin
            $entity = FormField::where('id', $id)->where('PPID', $checkLoginAsParty)
            ->with(['politicalPartyInfo' => function ($query) {
                $query->select('id', 'party_name');
            }])
            ->with(['formInfo' => function ($query) {
                $query->select('id', 'form_type');
            }])
            ->with(['formFieldOptionInfo' => function ($query) {
                $query->select('id', 'form_field_id', 'option');
            }])
            ->first();

            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }

            $entity->political_party = $entity->politicalPartyInfo->party_name;
            $entity->is_required = ($entity->is_required == 1?'true':'false');

            $politicalParties = PoliticalParty::where('status', 1)->pluck('party_name', 'id');
            $fieldTypes = getFieldTypes();
            $isRequiredTypes = getIsRequired();
            $customFieldTabs = getCustomFieldTabs();

            $isFormFieldUsed = 0;
            $chkFieldUsed = MemberExtraInfo::where("form_field_id", $id)->first();
            if(empty($chkFieldUsed)){
                $chkFieldUsedInSurvey = SurveyFeedback::where("form_field_id", $id)->first();
                if(!empty($chkFieldUsedInSurvey)){
                    $isFormFieldUsed = 1;
                }
            }else{
                $isFormFieldUsed = 1;
            }

            if(!empty($entity->formFieldOptionInfo) && count($entity->formFieldOptionInfo) > 0 && (in_array($entity->field_type, ['checkbox','radio','dropdown']))){
                $optionArr = [];
                foreach($entity->formFieldOptionInfo as $key => $optionVal){
                    $optionArr[] = trim($optionVal->option);
                }

                // If form is not used yet, then all fields will be editable
                if($isFormFieldUsed == 0){
                    $entity->field_options = implode(', ', $optionArr);
                    $entity->formFieldOptionInfo = "";
                }else{
                    $entity->formFieldOptionInfo = implode(', ', $optionArr);
                }
            }else{
                $entity->formFieldOptionInfo = "";
            }

            return view('commonadmin.form_customize_manage.edit',compact('entity', 'politicalParties', 'fieldTypes', 'isRequiredTypes', 'customFieldTabs', 'formType', 'isFormFieldUsed', 'formId'));
        }catch(\Exception $e){
         return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function update(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $getFieldTypesString = getFieldTypesString();
            $validator = Validator::make($request->all(), [
                'update_id' => 'required|exists:form_fields,id',
                'field_name' => 'required|max:50',
                'es_field_name' => 'required|max:50',
                'is_required' => 'required',
                'field_type' => 'nullable|in:'.$getFieldTypesString,
                'isFormFieldUsed' => 'nullable|in:0,1',
                'field_min_length' => 'nullable|numeric',
                'field_max_length' => 'nullable|numeric|gt:field_min_length',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                // start a transaction
                DB::beginTransaction();

                $editedFormField = FormField::where('id', $request->update_id)->where('PPID', $checkLoginAsParty)->first();
                if(empty($editedFormField)){
                    return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
                }

                // Check if field name exist for the same form type of political party
                $checkField = FormField::where('form_id', $editedFormField->form_id)->where('id', '!=', $request->update_id)->where('field_name', $request->field_name)->first();
                if(!empty($checkField)){
                    $errorObj = (object) [];
                    $errorObj->field_name = ["Field under this political party form already exist."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                if($request->isFormFieldUsed == 1){ // If field used by user
                    $editedFormField->field_name = $request->field_name;
                    $editedFormField->es_field_name = $request->es_field_name;
                    $editedFormField->save();

                    if(!empty($request->field_options) && in_array($editedFormField->field_type, ['checkbox','radio','dropdown'])){
                        $splitFieldOptions = explode(',', ($request->field_options));

                        if(empty($splitFieldOptions) || count($splitFieldOptions) == 0){
                            $errorObj = (object) [];
                            $errorObj->field_options = ["Field options must be in correct format."];
                            return redirect()->back()->withInput()->withErrors($errorObj);
                        }
                        // trim whitespace and apply the camel case function to each element of the array
                        $splitFieldOptions = array_map(function($item) {
                            return toCamelCase(trim($item));
                        }, $splitFieldOptions);

                        if(count(array_unique($splitFieldOptions)) < count($splitFieldOptions)){
                            $errorObj = (object) [];
                            $errorObj->field_options = ["Field options should not be same."];
                            return redirect()->back()->withInput()->withErrors($errorObj);
                        }else{
                            // Create Form Field Option
                            foreach($splitFieldOptions as $key => $optionVal){
                                // Check if field option is not used previously
                                $checkFieldOption = FormFieldOption::where('PPID', $editedFormField->PPID)->where('form_field_id', $editedFormField->id)->where('option', $optionVal)->first();
                                if(!empty($checkFieldOption)){
                                    $errorObj = (object) [];
                                    $errorObj->field_options = ["Field option (".$optionVal.") already exists for this field"];
                                    return redirect()->back()->withInput()->withErrors($errorObj);
                                }

                                $createFormFieldOption = FormFieldOption::create([
                                    'PPID' => $editedFormField->PPID,
                                    'form_field_id' => $editedFormField->id,
                                    'option'=> $optionVal,
                                ]);
                            }
                        }
                    }
                }else{
                    $editedFormField->field_name = $request->field_name;
                    $editedFormField->es_field_name = $request->es_field_name;
                    $editedFormField->tab_type = (!empty($request->tab_type)?$request->tab_type:NULL);
                    $editedFormField->is_required = ($request->is_required == 'true'?1:0);
                    $editedFormField->field_type = $request->field_type;
                    $editedFormField->field_min_length = (!empty($request->field_min_length)?$request->field_min_length:NULL);
                    $editedFormField->field_max_length = (!empty($request->field_max_length)?$request->field_max_length:NULL);

                    if(in_array($request->field_type, ['text','number','textarea'])){
                        $editedFormField->field_min_length = (!empty($request->field_min_length)?$request->field_min_length:NULL);
                        $editedFormField->field_max_length = (!empty($request->field_max_length)?$request->field_max_length:NULL);
                        $editedFormField->decimal_points = NULL;
                        if($request->field_type == 'text'){
                            $editedFormField->decimal_points = (!empty($request->decimal_points)?$request->decimal_points:NULL);
                        }
                        $editedFormField->save();
                    }else{
                        $editedFormField->field_min_length = NULL;
                        $editedFormField->field_max_length = NULL;
                        $editedFormField->decimal_points = NULL;
                        $editedFormField->save();
                    }

                    if(in_array($request->field_type, ['checkbox','radio','dropdown'])){
                        // Delete field options
                        $deleteFormFieldOption = FormFieldOption::where('PPID', $editedFormField->PPID)->where('form_field_id', $editedFormField->id)->get();

                        foreach ($deleteFormFieldOption as $formOptionRow) {
                            $formOptionRow->delete();
                        }

                        $splitFieldOptions = explode(',', ($request->field_options));

                        if(empty($splitFieldOptions) || count($splitFieldOptions) == 0){
                            $errorObj = (object) [];
                            $errorObj->field_options = ["Field options must be in correct format."];
                            return redirect()->back()->withInput()->withErrors($errorObj);
                        }
                        // trim whitespace and apply the camel case function to each element of the array
                        $splitFieldOptions = array_map(function($item) {
                            return toCamelCase(trim($item));
                        }, $splitFieldOptions);

                        if(count(array_unique($splitFieldOptions)) < count($splitFieldOptions)){
                            $errorObj = (object) [];
                            $errorObj->field_options = ["Field options should not be same."];
                            return redirect()->back()->withInput()->withErrors($errorObj);
                        }else{
                            // Create Form Field Option
                            foreach($splitFieldOptions as $key => $optionVal){
                                $createFormFieldOption = FormFieldOption::create([
                                    'PPID' => $editedFormField->PPID,
                                    'form_field_id' => $editedFormField->id,
                                    'option'=> $optionVal,
                                ]);
                            }
                        }
                    }else{
                        // Delete field options
                        $deleteFormFieldOption = FormFieldOption::where('PPID', $editedFormField->PPID)->where('form_field_id', $editedFormField->id)->get();

                        foreach ($deleteFormFieldOption as $formOptionRow) {
                            $formOptionRow->delete();
                        }
                    }
                }

                $reqFormId = null;
                if(!empty($request->formId)){
                    $reqFormId = $request->formId;
                }

                // commit the transaction
                DB::commit();
                return redirect()->route('list_form_customization',['formType' => $request->form_type, 'formId' => $reqFormId])->with('success','Political Party Form Field Updated Successfully.');
            }
        }catch(\Exception $e){
            // rollback the transaction in case of an error
            DB::rollback();
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update_form_customization_status(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $formField = FormField::where(["id"=>$request->id, "PPID"=>$checkLoginAsParty])->first();
            if(empty($formField)){
                $response['status'] = false;
                $response['message'] = "Un-authorized Access";
                return response()->json($response);
            }

            $status = ($formField->status==1) ? 0 : 1;
            $formField->status = $status;
            $formField->save();
            $response['status'] = true;
            $response['message'] = 'Form Field Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

}
?>