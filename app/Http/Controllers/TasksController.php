<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TasksController extends Controller
{
    public function getTasks(Request $request)
    {
        try {
            $tasks = Tasks::when(!empty($request->status),
             function ($query ) use ($request) {
                $query->where("status", $request->status);
            })->orderBy("created_at","desc")
                ->paginate(10,['*'],'',$request->page);
        } catch (Exception $e) {
            $e->getMessage();
        }
            
        if ($request->page > $tasks->lastPage()) {
            return response()->json(['message' => 'Invalid page no'],400);
        }

        $responseData = [
            "message"=> 'OK',
            "data"=> [
                "total"=> $tasks->total(),
                "current_page"=> $tasks->currentPage(),
                "last_page"=> $tasks->lastPage(),
                "showing_from"=> $tasks->firstItem() ?? "0",
                "showing_to"=> $tasks->lastItem() ?? "0",
                "list"=> collect($tasks->items())->map(function($task) {
                    return [
                        "id"=> "{$task->id}",
                        "title"=> $task->title,
                        "sender_name"=> $task->sender_name,
                        "sender_contact_no"=> $task->sender_contact_no,
                        "sender_address"=> $task->sender_address,
                        "sender_city"=> $task->sender_city,
                        "sender_location_url"=> $task->sender_location_url,
                        "recipient_name"=> $task->recipient_name,
                        "recipient_contact_no"=> $task->recipient_contact_no,
                        "recipient_address"=> $task->recipient_address,
                        "recipient_city"=> $task->recipient_city,
                        "recipient_location_url"=> $task->recipient_location_url,
                        "image_url"=> $task->image_url,
                        "remarks"=> $task->remarks,
                        "status"=> $task->status,
                        "created_at"=> $task->created_at->format("Y-m-d H:i:s"),
                        "updated_at"=> $task->updated_at->format("Y-m-d H:i:s"),
                    ];
                }),
            ]
        ];

        return response()->json($responseData);
    }

    public function getDispatchTasks(Request $request)
    {
        try {
            $user = $request->user();
            $tasks = Tasks::when(!empty($request->status),
                function ($query ) use ($request) {
                return $query->where("status", $request->status);
            })
                ->where(function ($query) use ($user) {
                    return $query->where("user_id", $user->id)
                                ->orWhereNull("user_id");
            })
                ->where('status','!=','CANCELLED')
                ->orderBy('created_at','desc')
                ->paginate(empty($request->size) ? 999 : 10,['*'],'',$request->page);

            if ($request->page > $tasks->lastPage()) {
                return response()->json(['message' => 'Invalid page no'],400);
            }

            return response()->json([
                "message"=> 'OK',
                "data"=> [
                    "total"=> $tasks->total(),
                    "current_page"=> $tasks->currentPage(),
                    "last_page"=> $tasks->lastPage(),
                    "showing_from"=> $tasks->firstItem() ?? "0",
                    "showing_to"=> $tasks->lastItem() ?? "0",
                    "list"=> collect($tasks->items())->map(function($task) {
                        return [
                            "id"=> "{$task->id}",
                            "title"=> $task->title,
                            "sender_name"=> $task->sender_name,
                            "sender_contact_no"=> $task->sender_contact_no,
                            "sender_address"=> $task->sender_address,
                            "sender_city"=> $task->sender_city,
                            "sender_location_url"=> $task->sender_location_url,
                            "recipient_name"=> $task->recipient_name,
                            "recipient_contact_no"=> $task->recipient_contact_no,
                            "recipient_address"=> $task->recipient_address,
                            "recipient_city"=> $task->recipient_city,
                            "recipient_location_url"=> $task->recipient_location_url,
                            "image_url"=> $task->image_url,
                            "remarks"=> $task->remarks,
                            "status"=> $task->status,
                            "created_at"=> $task->created_at->format("Y-m-d H:i:s"),
                            "updated_at"=> $task->updated_at->format("Y-m-d H:i:s"),
                        ];
                    }),
                ]
            ]);
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function getTask(Request $request)
    {
        if (!empty($request->id)) {
            $task = Tasks::find($request->id);

            if (
                $request->user()->type === "DISPATCH" &&
                ($request->user()->id !== $task->user_id ||
                $task->status !== "DELIVERY")
            ) {
                return response()->json(['message' => 'Unauthorized'],401);
            }

            if ($task) {
                $responseData = [
                    "message"=> 'OK',
                    "data"=> [
                        "id"=> "{$task->id}",
                        "title"=> $task->title,
                        "sender_name"=> $task->sender_name,
                        "sender_contact_no"=> $task->sender_contact_no,
                        "sender_address"=> $task->sender_address,
                        "sender_city"=> $task->sender_city,
                        "sender_location_url"=> $task->sender_location_url,
                        "recipient_name"=> $task->recipient_name,
                        "recipient_contact_no"=> $task->recipient_contact_no,
                        "recipient_address"=> $task->recipient_address,
                        "recipient_city"=> $task->recipient_city,
                        "recipient_location_url"=> $task->recipient_location_url,
                        "image_url"=> $task->image_url,
                        "remarks"=> $task->remarks,
                        "status"=> $task->status,
                        "created_at"=> $task->created_at->format("Y-m-d H:i:s"),
                        "updated_at"=> $task->updated_at->format("Y-m-d H:i:s"),
                    ]
                ];
                return response()->json($responseData);
            } else {
                return response()->json(["message"=> "Invalid id"], 400);
            }
        } else {
            return response()->json(["message"=> "Id field is required"],400);
        }
    }

    public function createUpdateTask(Request $request)
    {
        if (!empty($request->id)) {
            $task = Tasks::find($request->id);

            if ($task) {
                // if (!empty($request->title)) {
                //     $task->title = $request->title;
                // }
                if (!empty($request->sender_name)) {
                    $task->sender_name = $request->sender_name;
                }
                if (!empty($request->sender_contact_no)) {
                    $task->sender_contact_no = $request->sender_contact_no;
                }
                if (!empty($request->sender_address)) {
                    $task->sender_address = $request->sender_address;
                }
                if (!empty($request->sender_city)) {
                    $task->sender_city = $request->sender_city;
                }
                if (!empty($request->sender_location_url)) {
                    $task->sender_location_url = $request->sender_location_url;
                }
                if (!empty($request->recipient_name)) {
                    $task->recipient_name = $request->recipient_name;
                }
                if (!empty($request->recipient_contact_no)) {
                    $task->recipient_contact_no = $request->recipient_contact_no;
                }
                if (!empty($request->recipient_address)) {
                    $task->recipient_address = $request->recipient_address;
                }
                if (!empty($request->recipient_city)) {
                    $task->recipient_city = $request->recipient_city;
                }
                if (!empty($request->recipient_location_url)) {
                    $task->recipient_location_url = $request->recipient_location_url;
                }
                if (!empty($request->remarks)) {
                    $task->remarks = $request->remarks;
                }

                $task->save();

                return response()->json(['message'=> 'OK', 'data'=> (object)[]],200);
            } else {
                return response()->json(['message'=> 'Id not found'],400);
            }
        } 

        if (
            !empty($request->title) &&
            !empty($request->sender_name) &&
            !empty($request->sender_contact_no) &&
            !empty($request->sender_address) &&
            !empty($request->sender_city) &&
            !empty($request->sender_location_url) &&
            !empty($request->recipient_name) &&
            !empty($request->recipient_contact_no) &&
            !empty($request->recipient_address) &&
            !empty($request->recipient_city) &&
            !empty($request->recipient_location_url)
        ) {
            Tasks::create([
                'title'=> $request->title,
                'sender_name'=> $request->sender_name,
                'sender_contact_no'=> $request->sender_contact_no,
                'sender_address'=> $request->sender_address,
                'sender_city'=> $request->sender_city,
                'sender_location_url'=> $request->sender_location_url,
                'recipient_name'=> $request->recipient_name,
                'recipient_contact_no'=> $request->recipient_contact_no,
                'recipient_address'=> $request->recipient_address,
                'recipient_city'=> $request->recipient_city,
                'recipient_location_url'=> $request->recipient_location_url,
                'remarks'=> $request->remarks,
                'image_url'=> null,
                'status'=> 'PENDING',
            ]);

            return response()->json(['message'=> 'OK', 'data'=> (object)[]]);
        } else {
            return response()->json(['message'=> 'Invalid inputs'],400);
        };
    }

    public function claimTask(Request $request)
    {
        if (!empty($request->id)) {
            $task = Tasks::find($request->id);

            if ($task && $task->status === 'PENDING') {
                $task->status = 'DELIVERY';
                $task->user_id = $request->user()->id;

                $task->save();
            } else {
                return response()->json(['message'=> 'Invalid request'],400);
            }

            return response()->json(['message'=> 'OK', 'data'=> (object)[]],200);
        } else {
            return response()->json(['message'=> 'Id field is required'],400);
        }
    }

    public function cancelTask(Request $request)
    {
        if (!empty($request->id)) {
            $task = Tasks::find($request->id);

            if ($task && $task->status === 'PENDING') {
                $task->status = 'CANCELLED';

                $task->save();
            } else {
                return response()->json(['message'=> 'Invalid request'],400);
            }

            return response()->json(['message'=> 'OK', 'data'=> (object)[]],200);
        } else {
            return response()->json(['message'=> 'Id field is required'],400);
        }
    }

    public function completeTask(Request $request)
    {
        if (empty($request->id)||
            !Tasks::where('id', $request->id)->exists() ||
            Tasks::find($request->id)->user_id !== $request->user()->id
        ) {
            return response()->json(['message'=> 'Invalid id'],400);
        }

        $task = Tasks::find($request->id);

        if ($task->status !== 'DELIVERY' || !$request->hasFile('image')) {
            return response()->json(['message'=> 'Invalid request'],400);
        }

        if ($request->file('image')->getSize() > 5242880) {
            return response()->json(['message'=> 'File too large. Maximum size is 5MB'],413);
        }

        if (!in_array($request->file('image')->getMimeType(),['image/jpeg', 'image/png'])) {
            return response()->json(['message'=> 'Invalid file type. Allowed types are JPEG and PNG'],415);
        }

        $imagePath = $request->file('image')->store('storage','public');
        $task->image_url = asset(Storage::url($imagePath));
        $task->status = 'COMPLETED';

        $task->save();

        return response()->json(['message'=> 'OK', 'data'=> (object)[]],200);
    }
}