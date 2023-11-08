<?php

namespace App\Http\Controllers\Vehicle;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\Models\VehicleDocument;

class VehicleDocumentController extends BaseController
{
    public function index()
    {
        $vehicle_documents = VehicleDocument::all();

        if ($vehicle_documents->isEmpty()) {
            return $this->sendError('No Vehicle Documents Found');
        }

        return $this->sendSuccess($vehicle_documents);
    }

    // public function store(Request $request)
    // {
    //     $insurance_front_image_name = $request->input('insurance_front_image');
    //     $insurance_back_image = $request->input('insurance_back_image');
    //     $fitness_front_image = $request->input('fitness_front_image');
    //     $fitness_back_image = $request->input('fitness_back_image');
    //     $tax_front_image = $request->input('tax_front_image');
    //     $tax_back_image = $request->input('tax_back_image');
    //     $permit_front_image = $request->input('permit_front_image');
    //     $permit_back_image = $request->input('permit_back_image');
    //     $rc_front_image     = $request->input('rc_front_image');
    //     $rc_back_image = $request->input('rc_back_image');

    //     $fileContents = $request->getContent();

    //     $insurance_front_image_new_name = $request->input('vehicle_id') . '_' . 'insurance_front_image_name' . '.' . pathinfo($insurance_front_image_name, PATHINFO_EXTENSION);
    //     $insurance_back_image_new_name =  $request->input('vehicle_id') . '_' . 'insurance_back_image' . '.' . pathinfo($insurance_back_image, PATHINFO_EXTENSION);
    //     $fitness_front_new_image = $request->input('vehicle_id') . '_' . 'fitness_front_image' . '.' . pathinfo($fitness_front_image, PATHINFO_EXTENSION);
    //     $fitness_back_new_image = $request->input('vehicle_id') . '_' . 'fitness_back_image' . '.' . pathinfo($fitness_back_image, PATHINFO_EXTENSION);
    //     $tax_front_new_image = $request->input('vehicle_id') . '_' . 'tax_front_image' . '.' . pathinfo($tax_front_image, PATHINFO_EXTENSION);
    //     $tax_back_new_image = $request->input('vehicle_id') . '_' . 'tax_back_image' . '.' . pathinfo($tax_back_image, PATHINFO_EXTENSION);
    //     $permit_front_new_image = $request->input('vehicle_id') . '_' . 'permit_front_image' . '.' . pathinfo($permit_front_image, PATHINFO_EXTENSION);
    //     $permit_back_new_image = $request->input('vehicle_id') . '_' . 'permit_back_image' . '.' . pathinfo($permit_back_image, PATHINFO_EXTENSION);
    //     $rc_front_new_image = $request->input('vehicle_id') . '_' . 'rc_front_image' . '.' . pathinfo($rc_front_image, PATHINFO_EXTENSION);
    //     $rc_back_new_image = $request->input('vehicle_id') . '_' . 'rc_back_image' . '.' . pathinfo($rc_back_image, PATHINFO_EXTENSION);

    //     $success = file_put_contents(public_path('storage/uploads/' . $insurance_front_image_new_name), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $insurance_back_image_new_name), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $fitness_front_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $fitness_back_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $tax_front_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $tax_back_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $permit_front_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $permit_back_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $rc_front_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $rc_back_new_image), $fileContents);

    //     if ($success !== false) {
    //         $request->merge(['insurance_front_image' => $insurance_front_image_new_name]);
    //         $request->merge(['insurance_back_image' => $insurance_back_image_new_name]);
    //         $request->merge(['fitness_front_image' => $fitness_front_new_image]);
    //         $request->merge(['fitness_back_image' => $fitness_back_new_image]);
    //         $request->merge(['tax_front_image' => $tax_front_new_image]);
    //         $request->merge(['tax_back_image' => $tax_back_new_image]);
    //         $request->merge(['permit_front_image' => $permit_front_new_image]);
    //         $request->merge(['permit_back_image' => $permit_back_new_image]);
    //         $request->merge(['rc_front_image' => $rc_front_new_image]);
    //         $request->merge(['rc_back_image' => $rc_back_new_image]);

    //         $vehicle_document = VehicleDocument::create($request->all());

    //         if ($vehicle_document) {
    //             return $this->sendSuccess("Vehicle Document Uploaded Successfully");
    //         } else {
    //             return $this->sendError('Failed to Upload Vehicle Document');
    //         }
    //     } else {
    //         return $this->sendError('Failed to Upload the Vehicle Document', [], 500);
    //     }
    // }

    // public function show($id)
    // {
    //     $vehicle_document = VehicleDocument::where('vehicle_id', $id)->get();

    //     if (!$vehicle_document) {
    //         return $this->sendError('Vehicle Document Not Found');
    //     }

    //     return $this->sendSuccess($vehicle_document);
    // }

    // public function update(Request $request, $id)
    // {
    //     $dealer = VehicleDocument::findOrFail($id);

    //     $insurance_front_image_name = $request->insurance_front_image;
    //     $insurance_back_image = $request->insurance_back_image;
    //     $fitness_front_image = $request->fitness_front_image;
    //     $fitness_back_image = $request->fitness_back_image;
    //     $tax_front_image = $request->tax_front_image;
    //     $tax_back_image = $request->tax_back_image;
    //     $permit_front_image = $request->permit_front_image;
    //     $permit_back_image = $request->permit_back_image;
    //     $rc_front_image     = $request->rc_front_image;
    //     $rc_back_image = $request->rc_back_image;

    //     $fileContents = $request->getContent();

    //     $insurance_front_image_new_name = $dealer->vehicle_id . '_' . 'insurance_front_image_name' . '.' . pathinfo($insurance_front_image_name, PATHINFO_EXTENSION);
    //     $insurance_back_image_new_name =  $dealer->vehicle_id . '_' . 'insurance_back_image' . '.' . pathinfo($insurance_back_image, PATHINFO_EXTENSION);
    //     $fitness_front_new_image = $dealer->vehicle_id . '_' . 'fitness_front_image' . '.' . pathinfo($fitness_front_image, PATHINFO_EXTENSION);
    //     $fitness_back_new_image = $dealer->vehicle_id . '_' . 'fitness_back_image' . '.' . pathinfo($fitness_back_image, PATHINFO_EXTENSION);
    //     $tax_front_new_image = $dealer->vehicle_id . '_' . 'tax_front_image' . '.' . pathinfo($tax_front_image, PATHINFO_EXTENSION);
    //     $tax_back_new_image = $dealer->vehicle_id . '_' . 'tax_back_image' . '.' . pathinfo($tax_back_image, PATHINFO_EXTENSION);
    //     $permit_front_new_image = $dealer->vehicle_id . '_' . 'permit_front_image' . '.' . pathinfo($permit_front_image, PATHINFO_EXTENSION);
    //     $permit_back_new_image = $dealer->vehicle_id . '_' . 'permit_back_image' . '.' . pathinfo($permit_back_image, PATHINFO_EXTENSION);
    //     $rc_front_new_image = $dealer->vehicle_id . '_' . 'rc_front_image' . '.' . pathinfo($rc_front_image, PATHINFO_EXTENSION);
    //     $rc_back_new_image = $dealer->vehicle_id . '_' . 'rc_back_image' . '.' . pathinfo($rc_back_image, PATHINFO_EXTENSION);

    //     $success = file_put_contents(public_path('storage/uploads/' . $insurance_front_image_new_name), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $insurance_back_image_new_name), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $fitness_front_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $fitness_back_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $tax_front_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $tax_back_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $permit_front_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $permit_back_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $rc_front_new_image), $fileContents);
    //     $success = file_put_contents(public_path('storage/uploads/' . $rc_back_new_image), $fileContents);

    //     if ($success !== false) {
    //     }
    //     $dealer->fill($request->only([
    //         'vehicle_id',
    //         'policy_no',
    //         'insurance_company_name',
    //         'insurance_type',
    //         'insurance_start_date',
    //         'insurance_expiry_date',
    //         'fitness_certificate_expiry_date',
    //         'tax_expiry_date',
    //         'permit_expiry_date',
    //         'rc_expiry_date',
    //         'status',
    //         'updated_by',
    //         'ip_address'
    //     ]));

    //     $dealer->insurance_front_image = $insurance_front_image_new_name;
    //     $dealer->insurance_back_image = $insurance_back_image_new_name;
    //     $dealer->fitness_front_image = $fitness_front_new_image;
    //     $dealer->fitness_back_image = $fitness_back_new_image;
    //     $dealer->tax_front_image = $tax_front_new_image;
    //     $dealer->tax_back_image = $tax_back_new_image;
    //     $dealer->permit_front_image = $permit_front_new_image;
    //     $dealer->permit_back_image = $permit_back_new_image;
    //     $dealer->rc_front_image = $rc_front_new_image;
    //     $dealer->rc_back_image = $rc_back_new_image;

    //     if ($dealer->save()) {
    //         return $this->sendSuccess("Vehicle Document Uploaded Successfully");
    //     } else {
    //         return $this->sendError('Failed to Upload Vehicle Document');
    //     }
    // }

    public function destroy(Request $request, $id)
    {
        $vehicle_document = VehicleDocument::find($id);

        if (!$vehicle_document) {
            return $this->sendError('Vehicle Document Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vehicle_document->status = 0;
        $vehicle_document->deleted_by = $request->deleted_by;
        $vehicle_document->save();
        if ($vehicle_document->delete()) {
            return $this->sendSuccess('Vehicle Document Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Vehicle Document');
        }
    }

    // public function upload_vehicle_document(Request $request)
    // {
    //     //Validation Code
    //     $validator = Validator::make($request->all(), [
    //         'vehicle_id' => 'required',
    //         'login_image1' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'login_image2' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     ]);
    //     if ($validator->fails()) {
    //         $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
    //         return response()->json($response, 403);
    //     }

    //     $vehicle_document = VehicleDocument::find($request->input('vehicle_id'));
    //     $vehicle = Vehicle::where('id', $request->input('vehicle_id'))->first();

    //     if (!$vehicle_document && !$vehicle) {
    //         $response = ["success" => false, "message" => "Vehicle Not Found", "status_code" => 404];
    //         return response()->json($response, 404);
    //     } else {
    //         $fileContents = $request->getContent();


    //         $insurance_front_image = $vehicle->vehicle_name . '_insurance_front_image.' . $request->insurance_front_image->extension();

    //         // $insurance_front_image = $vehicle->vehicle_name . '_insurance_front_image_name.' . $request->file('insurance_front_image')->getClientOriginalExtension();
    //         // $insurance_front_image_loc = file_put_contents(public_path('storage/uploads/' . $insurance_front_image), $fileContents);

    //         $insurance_front_image_loc = $request->insurance_front_image->storeAs('public/uploads/', $insurance_front_image);
    //         $insurance_back_image = $vehicle->vehicle_name . '_insurance_back_image.' . $request->insurance_back_image->extension();
    //         $insurance_back_image_loc = $request->insurance_back_image->storeAs('public/uploads/', $insurance_back_image);
    //         $fitness_front_image = $vehicle->vehicle_name . '_fitness_front_image.' . $request->fitness_front_image->extension();
    //         $fitness_front_image_loc = $request->fitness_front_image->storeAs('public/uploads/', $fitness_front_image);
    //         $fitness_back_image = $vehicle->vehicle_name . '_fitness_back_image.' . $request->fitness_back_image->extension();
    //         $fitness_back_image_loc = $request->fitness_back_image->storeAs('public/uploads/', $fitness_back_image);
    //         $tax_front_image = $vehicle->vehicle_name . '_tax_front_image.' . $request->tax_front_image->extension();
    //         $tax_front_image_loc = $request->tax_front_image->storeAs('public/uploads/', $tax_front_image);
    //         $tax_back_image = $vehicle->vehicle_name . '_tax_back_image.' . $request->tax_back_image->extension();
    //         $tax_back_image_loc = $request->tax_back_image->storeAs('public/uploads/', $tax_back_image);
    //         $permit_front_image = $vehicle->vehicle_name . '_permit_front_image.' . $request->permit_front_image->extension();
    //         $permit_front_image_loc =  $request->permit_front_image->storeAs('public/uploads/', $permit_front_image);
    //         $permit_back_image = $vehicle->vehicle_name . '_permit_back_image.' . $request->permit_back_image->extension();
    //         $permit_back_image_loc = $request->permit_back_image->storeAs('public/uploads/', $permit_back_image);
    //         $rc_front_image = $vehicle->vehicle_name . '_rc_front_image.' . $request->rc_front_image->extension();
    //         $rc_front_image_loc = $request->rc_front_image->storeAs('public/uploads/', $rc_front_image);
    //         $rc_back_image = $vehicle->vehicle_name . '_rc_back_image.' . $request->rc_back_image->extension();
    //         $rc_back_image_loc = $request->rc_back_image->storeAs('public/uploads/', $rc_back_image);

    //         $vehicle_document->insurance_front_image = $insurance_front_image_loc;
    //         $vehicle_document->insurance_back_image = $insurance_back_image_loc;
    //         $vehicle_document->fitness_front_image = $fitness_front_image_loc;
    //         $vehicle_document->fitness_back_image = $fitness_back_image_loc;
    //         $vehicle_document->tax_front_image = $tax_front_image_loc;
    //         $vehicle_document->tax_back_image = $tax_back_image_loc;
    //         $vehicle_document->permit_front_image = $permit_front_image_loc;
    //         $vehicle_document->permit_back_image = $permit_back_image_loc;
    //         $vehicle_document->rc_front_image = $rc_front_image_loc;
    //         $vehicle_document->rc_back_image = $rc_back_image_loc;
    //         $vehicle_document->policy_no = $request->input('policy_no');
    //         $vehicle_document->insurance_company_name = $request->input('insurance_company_name');
    //         $vehicle_document->insurance_type = $request->input('insurance_type');
    //         $vehicle_document->insurance_start_date = $request->input('insurance_start_date');
    //         $vehicle_document->insurance_expiry_date = $request->input('insurance_expiry_date');
    //         $vehicle_document->fitness_certificate_expiry_date = $request->input('fitness_certificate_expiry_date');
    //         $vehicle_document->tax_expiry_date = $request->input('tax_expiry_date');
    //         $vehicle_document->permit_expiry_date = $request->input('permit_expiry_date');
    //         $vehicle_document->rc_expiry_date = $request->input('rc_expiry_date');
    //         $result = $vehicle_document->update();
    //         if ($result) {
    //             $response = ["success" => true, "message" => "Vehicle Document Uploaded Successfully", "status_code" => 200];
    //             return response()->json($response, 200);
    //         } else {
    //             $response = ["success" => false, "message" => "No Documents Uploaded", "status_code" => 404];
    //             return response()->json($response, 404);
    //         }
    //     }
    // }


    public function vehicleimageUpload(Request $request)
    {
        $vehicle = VehicleDocument::where('vehicle_id', $request->input('vehicle_id'))->first();

        try {
            $updatedImages = [];

            $columnMappings = [
                'insurance_front_image' => 'insurance_front_image',
                'insurance_back_image' => 'insurance_back_image',
                'fitness_front_image' => 'fitness_front_image',
                'fitness_back_image' => 'fitness_back_image',
                'tax_front_image' => 'tax_front_image',
                'tax_back_image' => 'tax_back_image',
                'permit_front_image' => 'permit_front_image',
                'permit_back_image' => 'permit_back_image',
                'rc_front_image' => 'rc_front_image',
                'rc_back_image' => 'rc_back_image',
            ];

            foreach ($columnMappings as $inputName => $dbColumn) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $validationRules = [
                        $inputName => 'image|mimes:jpeg,png|max:2048',
                    ];
                    $this->validate($request, $validationRules);
                    $fileName = $request->input('vehicle_id') . '_' . $dbColumn . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                    $filePath = $file->storeAs('post_img', $fileName, 'public');
                    $updatedImages[$dbColumn] = $filePath;
                }
            }

            $updatedImages['policy_no'] = $request->input('policy_no');
            $updatedImages['insurance_company_name'] = $request->input('insurance_company_name');
            $updatedImages['insurance_type'] = $request->input('insurance_type');
            $updatedImages['insurance_start_date'] = $request->input('insurance_start_date');
            $updatedImages['insurance_expiry_date'] = $request->input('insurance_expiry_date');
            $updatedImages['fitness_certificate_expiry_date'] = $request->input('fitness_certificate_expiry_date');
            $updatedImages['tax_expiry_date'] = $request->input('tax_expiry_date');
            $updatedImages['permit_expiry_date'] = $request->input('permit_expiry_date');
            $updatedImages['rc_expiry_date'] = $request->input('rc_expiry_date');

            if (!empty($updatedImages)) {
                $vehicle->update($updatedImages);
            }

            return response()->json(['status' => true, 'message' => "Images uploaded successfully"]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function vehicleimageRetrieve(Request $request)
    {

        $vehicle = VehicleDocument::where('vehicle_id', $request->input('vehicle_id'))->first();


        $imageData = [];

        $imageColumns = [
            'insurance_front_image',
            'insurance_back_image',
            'fitness_front_image',
            'fitness_back_image',
            'tax_front_image',
            'tax_back_image',
            'permit_front_image',
            'permit_back_image',
            'rc_front_image',
            'rc_back_image'
        ];

        // foreach ($imageColumns as $column) {
        //     $imagePath = $vehicle[$column];

        //     if ($imagePath) {
        //         $imageUrl = storage_path('app/public/' . $imagePath);
        //         if (File::exists($imageUrl)) {
        //             $fileContents = File::get($imageUrl);
        //             $base64 = base64_encode($fileContents);
        //             $imageData[$column] = 'data:image/jpeg;base64,' . $base64;
        //         } else {
        //             $imageData[$column] = '';
        //         }
        //     } else {
        //         $imageData[$column] = '';
        //     }
        // }

        foreach ($imageColumns as $column) {
            $imagePath = $vehicle[$column];

            if ($imagePath) {
                $imageUrl = asset("storage/$imagePath");

                $imageData[$column] = $imageUrl; // Assign the URL path to the image data
            } else {
                $imageData[$column] = ''; // No image path provided, set it to an empty string
            }
        }

        $imageData['policy_no'] = $vehicle->policy_no;
        $imageData['insurance_company_name'] = $vehicle->insurance_company_name;
        $imageData['insurance_type'] = $vehicle->insurance_type;
        $imageData['insurance_start_date'] = $vehicle->insurance_start_date;
        $imageData['insurance_expiry_date'] = $vehicle->insurance_expiry_date;
        $imageData['fitness_certificate_expiry_date'] = $vehicle->fitness_certificate_expiry_date;
        $imageData['tax_expiry_date'] = $vehicle->tax_expiry_date;
        $imageData['permit_expiry_date'] = $vehicle->permit_expiry_date;
        $imageData['rc_expiry_date'] = $vehicle->rc_expiry_date;
        $imageData['vehicle_id'] = $vehicle->vehicle_id;

        if (!empty($imageData)) {
            return response()->json(['success' => true, 'message' => $imageData]);
        } else {
            return response()->json(['message' => 'Images not found'], 404);
        }
    }
}
