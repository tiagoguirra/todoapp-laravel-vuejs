<?php

namespace App\Http\Controllers;

use Auth;
use baseController;
use App\Task;
use ModelForApi;
use ModelFilter;
use Validator;
use Illuminate\Http\Request;

class TaskController extends baseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * This function fetch the all task
     * 
     */
    public function index(Request $request){                
        $data = ModelFilter::setFilter(new Task,$request->query())
        ->applySearch(['name','description'])
        ->applyStatus('completed')
        ->applyPriority('priority')
        ->applyOrder()
        ->applyShow()
        ->applyUser()    
        ->makeToApi();          
        
        return $this->sendResponse('Ok',$data);
    }

    public function show($id){
        $data = Task::where('id',$id)->where('user_id',Auth::user()->id)->first();
        if($data){
            return $this->sendResponse('Ok',ModelForApi::getElement($data));
        }
        return $this->sendError('Not found','Not found by id '.$id,404);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'name'=>'required|string|max:255',
            'description'=>'nullable|string|max:255',
            'priority'=>'nullable|integer',
            'completed'=>'nullable|boolean'
        ]);

        if ($validate->fails()) {
            return $this->sendValidationError($validate->errors());
        }
        $priority = $request->priority!=null?$request->priority:0;
        $completed = $request->completed!=null?$request->completed:0;

        $task = Task::create([
            'name'=>$request->name,
            'description'=>$request->description,
            'priority'=>$priority,
            'completed'=>$completed,
            'user_id'=>Auth::user()->id
        ]);
        if($task){
            return $this->sendResponse('Created',ModelForApi::getElement($task),201);
        }
        return $this->sendError('Bad request','failure to save task',400);
    }

    public function update(Request $request,$id){
        $validate = Validator::make($request->all(),[
            'name'=>'required|string|max:255',
            'description'=>'nullable|string|max:255',
            'priority'=>'nullable|integer',
            'completed'=>'nullable|boolean'
        ]);

        if ($validate->fails()) {
            return $this->sendValidationError($validate->errors());
        }
        $task = Task::where('id',$id)->where('user_id',Auth::user()->id)->first();
        if($task){
            $priority = $request->priority!=null?$request->priority:0;
            $completed = $request->completed!=null?$request->completed:0;

            $task->name = $request->name;
            $task->description = $request->description;
            $task->priority = $priority;
            $task->completed = $completed;

            if($task->save()){
                return $this->sendResponse('Updated',ModelForApi::getElement($task),200);
            }
            return $this->sendError('Bad request','failure to save task',400);
        }        
        return $this->sendError('Bad request','Task no found by id '.$id,404);
    }

    public function delete($id){
        $task = Task::where('id',$id)->where('user_id',Auth::user()->id)->first();
        if($task){
            if($task->delete()){
                return $this->sendResponse('Deleted','successfully deleted task',200);
            }
            return $this->sendError('Bad request','failure to delete task',400);
        }
        return $this->sendError('Bad request','Task no found by id '.$id,404);
    }

    public function setCompleted($id){
        $task = Task::where('id',$id)->where('user_id',Auth::user()->id)->first();
        if($task){
            $task->completed = 1;
            if($task->save()){
                return $this->sendResponse('Updated',ModelForApi::getElement($task),200);
            }
            return $this->sendError('Bad request','failure to update task',400);
        }
        return $this->sendError('Bad request','Task no found by id '.$id,404);
    }
    public function setNotCompleted($id){
        $task = Task::where('id',$id)->where('user_id',Auth::user()->id)->first();
        if($task){
            $task->completed = 0;
            if($task->save()){
                return $this->sendResponse('Updated',ModelForApi::getElement($task),200);
            }
            return $this->sendError('Bad request','failure to update task',400);
        }
        return $this->sendError('Bad request','Task no found by id '.$id,404);
    }
}
