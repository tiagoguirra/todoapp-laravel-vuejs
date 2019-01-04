const filterMixin = {
    data: () => ({
        filters: {
            paginate: "true",
            pageId: "?page=1",
            perpage: 30,
            search: "",
            status: "",
            priority: "",
            order:{
                column:'created_at',
                order:'asc'
            }
        }
    }),
    methods: {
        makeFilter(filter) {            
            this.filters = { ...this.filters, ...filter }            
            return this.sendFilter()
        },
        sendFilter() {
            let filter = `${this.filters.pageId}`;
            filter += `&perpage=${this.filters.perpage}`;
            filter += `&status=${this.filters.status}`;
            filter += `&priority=${this.filters.priority}`;
            filter += `&search=${this.filters.search}`;            
            filter += `&order=${this.filters.order.column},${this.filters.order.order}`            
            return filter;
        }
    }
}

export default filterMixin