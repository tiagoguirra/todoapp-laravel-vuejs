<template>
  <div class="filters">
    <section class="container filter-container">
      <Search v-on:searchBy="onSearch" v-on:clear="onClear" v-if="search"/>      
      <custom-filter v-if="perpage.active!==false">
        <template slot="btn">Show Pages</template>
        <template slot="body">
          <ul class="perpage">
            <li
              v-for="(item,index) in perpage.value"
              :key="index"
              @click="setPerPage(item)"
              :class="filters.perpage==item?'active':''"
            >Show {{item}}</li>
          </ul>
        </template>
      </custom-filter>
      <custom-filter v-if="status.active!==false">
        <template slot="btn">Status</template>
        <template slot="body">
          <ul class="status">
            <li
              v-for="(index,item) in status.value"
              :key="index"              
              :class="filters.status===index?'active':''"
            ><input class="form__input" type="checkbox" v-model="checkbox.status" :value="index" @change="setStatus"/> {{item}}</li>
          </ul>
        </template>
      </custom-filter> 
      <custom-filter v-if="status.active!==false">
        <template slot="btn">Priority</template>
        <template slot="body">
          <ul class="priority">
            <li
              v-for="(index,item) in priority.value"
              :key="index"              
              :class="filters.priority===index?'active':''"
            ><input class="form__input" type="checkbox" v-model="checkbox.priority" :value="index" @change="setPriority"/> {{item}}</li>
          </ul>
        </template>
      </custom-filter>      
    </section>
    <slot name="table"></slot>
    <section class="container paginate-container">
      <Paginate :pageItem="paginate" v-on:setPage="setPage" v-if="paginate!=null"/>
    </section>            
  </div>
</template>
<script>
import CustomFilter from "@/components/template/CustomFilter";
import Search from "@/components/template/Search";
import Paginate from "@/components/template/Paginate";

export default {
  name: "Filters",
  components: { CustomFilter, Search, Paginate },
  props: {
    search: {
      type: Boolean,
      default: true
    },
    perpage: {
      type: Object,
      default: {
        active: 30,
        value: [5, 10, 20, 30]
      }
    },    
    status: {
      type: Object,
      default: {
        active: [1,0],
        value: {
          Active: 1,
          Unactive: 0
        }
      },      
    },
    priority: {
      type: Object,
      default: {
        active: [0,1,2],
        value: {
          Normal: 0,
          'Low priority': 1,
          'High priority': 2
        }
      }
    },
    paginate: null
  },
  data: () => ({  
    checkbox:{
      status:[],
      priority:[]
    },
    filters: {
      pageId: "?page=1",
      perpage: 30,
      search: "",
      status: "",
      priority:""
    }
  }),
  methods: {
    setPage(pageId) {
      this.filters.pageId = pageId;
      this.$emit("changeFilter", { pageId });
      this.sendFilter();
    },
    setPerPage(perpage) {
      this.filters.perpage = perpage;
      this.filters.pageId = "?page=1";
      this.$emit("changeFilter", { perpage, pageId: "?page=1" });
    },
    setStatus() {
      this.filters.status = this.checkbox.status.join(',');      
      this.filters.pageId = "?page=1";
      this.$emit("changeFilter", { status:this.filters.status, pageId: "?page=1" });
      this.sendFilter();
    },
    setPriority() {
      this.filters.priority = this.checkbox.priority.join(',');      
      this.filters.pageId = "?page=1";
      this.$emit("changeFilter", { priority:this.filters.priority, pageId: "?page=1" });
      this.sendFilter();
    },
    onSearch(search) {
      this.filters.search = search;
      this.filters.pageId = "?page=1";
      this.$emit("changeFilter", { search, pageId: "?page=1" });
      this.sendFilter();
    },
    onClear() {
      this.filters.search = "";
      this.filters.pageId = "?page=1";
      this.$emit("changeFilter", { search: "", pageId: "?page=1" });
      this.sendFilter();
    },
    makeFilter() {
      let filter = `${this.filters.pageId}`;      
      if (this.perpage.active) {        
        filter += `&perpage=${this.filters.perpage}`;
      }
      if (this.status.active!==false) {
        this.filters.status = this.status.active.join(',');
        filter += `&status=${this.filters.status}`;
      }
      if (this.search) {
        filter += `&search=${this.filters.search}`;
      }

       if (this.priority) {
        filter += `&priority=${this.filters.priority}`;
      }
      return filter;
    },
    sendFilter() {
      this.$emit("changeFilter", this.filters);
      this.$emit("filter", this.makeFilter());      
    }
  },
  mounted() {        
    this.filters.status = this.status.active!==false?this.status.active.join(','):''; 
    this.filters.priority = this.priority.active!==false?this.priority.active.join(','):''; 
    this.checkbox.status = this.status.active;
    this.checkbox.priority = this.priority.active;
    this.sendFilter();
  }
};
</script>
<style>
.container.content-overflow .filter-container {
  padding: 0 75px 0 75px;
}
.filter-container {
  display: flex;
  justify-content: space-between;
}

.status li,.priority li{
  text-align: left!important;
  padding: 15px!important;
}

@media (max-width: 720px) {
  .filter-container {
    flex-direction: column;
  }
  .filter-container .nav-search {
    padding-bottom: 15px;
  }
  .container.content-overflow .filter-container {
    padding: 0 10px 0 10px;
  }
}
</style>
