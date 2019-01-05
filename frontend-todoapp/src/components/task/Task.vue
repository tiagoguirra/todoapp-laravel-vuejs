<template>
  <div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-search">
          <Search
            :add="true"
            v-on:searchBy="getSearch"
            v-on:clear="clearSearch"
            v-on:addIt="createTask"
          />
        </div>
        <div class="form-filters">
          <Filters
            :search="false"
            :perpage="{active:30,value:[5, 10, 20, 30]}"
            :status="{active:[0,1],value:{Close:1,Open:0}}"
            :priority="{active:[0,1,2],value:{'No priority':0,'Low priority':1,'High priority':2}}"
            v-on:changeFilter="getFilter"
          />
        </div>
        <div class="card">
          <div class="card-body">
            <TableOrder
              :tableHeaderFilter="{id:'Id',name:'Name',priority:'Priority',created_at:'Created',option:'Option'}"
              v-on:setOrderTable="getFilter"
            >
              <template slot="list">
                <tr v-for="item in getTaskList" :key="item.id">
                  <th scope="row">{{item.id}}</th>
                  <td :class="{'task-completed':item.completed}">{{item.name}}</td>
                  <td :class="{'task-completed':item.completed}">{{item.priority==0?'No priority':item.priority==1?'Low priority':'High priority'}}</td>                  
                  <th :class="{'task-completed':item.completed}">{{item.created_at}}</th>
                  <th class="options">                    
                    <a class="option-btn" @click="showTask(item)">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a class="option-btn" @click="editTask(item)">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a class="option-btn" @click="deleteTask(item.id)">
                      <i class="fas fa-trash"></i>
                    </a>
                    <a class="option-btn" @click="notCompletedTask(item.id)" v-if="item.completed">
                      <i class="fas fa-undo-alt"></i>
                    </a>
                    <a class="option-btn" @click="completedTask(item.id)" v-else>
                      <i class="fas fa-check"></i>
                    </a>
                  </th>
                </tr>
              </template>
            </TableOrder>
            <Paginate :pageItem="getTaskPaginate" v-on:setPage="getFilter" v-if="getTaskPaginate!=null"/>
          </div>
        </div>
      </div>      
    </div>
  </div>
</template>

<script>
import Search from "@/components/template/Search";
import Filters from "@/components/template/Filters";
import CustomFilter from "@/components/template/CustomFilter";
import filterMixin from "@/mixins/filterMixin";
import TableOrder from "@/components/template/TableOrder";
import Paginate from "@/components/template/Paginate";
import { TASK_FETCH, TASK_CREATE, TASK_UPDATE, TASK_DELETE,TASK_COMPLETED,TASK_NOT_COMPLETED} from "@/store/actions/task";
import { mapGetters } from "vuex";

export default {
  name: "Task",
  components: { Search, Filters, CustomFilter, TableOrder,Paginate },
  mixins: [filterMixin],
  data: () => ({
    filter: ""
  }),
  computed: {
    ...mapGetters(["getTaskList", "getTaskPaginate"])
  },
  methods: {
    getData() {
      this.$store.dispatch(TASK_FETCH, { filter: this.filter });
    },
    getFilter(filters) {
      this.filter = this.makeFilter(filters);
      this.getData()
    },
    getSearch(searchBy) {      
      this.filter = this.makeFilter({ search: searchBy });
      this.getData()
    },
    clearSearch() {
      this.filter = this.makeFilter({ search: "" });
      this.getData()
    },    
    deleteTask(id) {      
      this.$store.dispatch(TASK_DELETE, { param: id }).then(resp => {
        this.getData();
        this.$vueOnToast.pop("success", "Deleted");
      });
    },
    notCompletedTask(id){
      this.$store.dispatch(TASK_NOT_COMPLETED,{param:id})
      .then(resp=>{
        this.getData()
        this.$vueOnToast.pop("success", "Updated");
      })
    },
    completedTask(id){
      this.$store.dispatch(TASK_COMPLETED,{param:id})
      .then(resp=>{
        this.getData()
        this.$vueOnToast.pop("success", "Updated");
      })
    },
    editTask(task) {
      console.log(task);
      this.$swal({
        title: "Edit task",
        showCloseButton: true,
        showCancelButton: true,        
        confirmButtonText: "Update",
        html: 
        ` <div class="form-group">
            <label for="nameTask">Name</label>
            <input id="nameTask" type="text" class="form-control" placeholder="Task name" value="${task.name}">          
          </div>
          <div class="form-group">
            <label for="descriptionTask">Description</label>
            <input id="descriptionTask" type="text" class="form-control" placeholder="Task description" value="${task.description}">          
          </div>
          <div class="form-group">
            <label for="priorityTask">Priority</label>
            <select id="priorityTask" class="form-control">
              <option selected>Priority</option>
              <option value='0' ${task.priority==0?'selected':''}>No priority</option>
              <option value='1' ${task.priority==1?'selected':''}>Low priority</option>
              <option value='2' ${task.priority==2?'selected':''}>High priority</option>
            </select>
          </div>
          <div class="form-group">
            <label for="statusTask">State</label>
            <select id="statusTask" class="form-control">
              <option selected>Status</option>
              <option value='1' ${task.completed==1?'selected':''}>Completed</option>
              <option value='0' ${task.completed==0?'selected':''}>Not complete</option>              
            </select>
          </div>
        `,
        preConfirm: () => {
          return {
            id: task.id,
            name: document.getElementById("nameTask").value,
            description: document.getElementById("descriptionTask").value,
            priority: document.getElementById("priorityTask").value,
            completed: document.getElementById("statusTask").value,
          }
        }
      })
      .then(data=>{
        if(data.value!=null){
          this.updateTask(data.value)
        }
      })
    },
    updateTask(task){
      this.$store.dispatch(TASK_UPDATE,{param:task.id,body:task})
      .then(resp=>{
        this.$vueOnToast.pop("success", "Updated");
        this.getData()
      })
    },
    createTask(name){
      this.$swal({
        title: "Create task",
        showCloseButton: true,
        showCancelButton: true,        
        confirmButtonText: "Save",
        html: 
        ` <div class="form-group">
            <label for="nameTask">Name</label>
            <input id="nameTask" type="text" class="form-control" placeholder="Task name" value="${name}">          
          </div>
          <div class="form-group">
            <label for="descriptionTask">Description</label>
            <input id="descriptionTask" type="text" class="form-control" placeholder="Task description">          
          </div>
          <div class="form-group">
            <label for="priorityTask">Priority</label>
            <select id="priorityTask" class="form-control">              
              <option value='0'selected >No priority</option>
              <option value='1' >Low priority</option>
              <option value='2' >High priority</option>
            </select>
          </div>
          <div class="form-group">
            <label for="statusTask">State</label>
            <select id="statusTask" class="form-control">              
              <option value='1'>Completed</option>
              <option value='0' selected>Not complete</option>              
            </select>
          </div>
        `,
        preConfirm: () => {
          return {            
            name: document.getElementById("nameTask").value,
            description: document.getElementById("descriptionTask").value,
            priority: document.getElementById("priorityTask").value,
            completed: document.getElementById("statusTask").value,
          }
        }
      })
      .then(data=>{
        if(data.value!=null){
          this.saveTask(data.value)
        }
      })
    },
    saveTask(task) {
      console.log(task);
      this.$store.dispatch(TASK_CREATE, task).then(resp => {
        this.$vueOnToast.pop("success", "Saved");
        this.getData();
      });
    },
    showTask(task){      
      this.$swal({
        title: "Create task",
        showCloseButton: true,
        showCancelButton: true,                
        html: 
        ` <div class="form-group">
            <h5 class="bold">Task name:</h5>
            <h5 class="light">${task.name}</h5>            
          </div>
          <div class="form-group">
            <h5 class="bold">Task description:</h5>
            <h5 class="light">${task.description}</h5>            
          </div>
          <div class="form-group">
            <h5 class="bold">Task priority:</h5>
            <h5 class="light">${task.priority==0?'No priority':task.priority==1?'Low priority':'High priority'}</h5> 
          </div>
          <div class="form-group">
            <h5 class="bold">Task status:</h5>
            <h5 class="light">${task.completed==1?'Completed':'Not complete'}</h5> 
          </div>
        `        
      })
    }
  },
  created() {
    this.getData();
  }
};
</script>

<style>
.card {
  border-radius: 15px;
}
.form-search,
.form-filters {
  padding-bottom: 15px;
}
.swal2-popup #swal2-content .form-group{
  text-align: left;
}

.swal2-popup #swal2-content .form-group .light {
  color: var(--grey-rtc);
  font-weight: 100;
}
.swal2-popup #swal2-content .form-group .bold {
  font-weight: 700;
  color: #484848;
}
.task-completed{
  color:#ccc;
  text-decoration: line-through;
}
</style>