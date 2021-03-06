@extends('layouts.app')

@section('title', 'Processes List')

@section('styles')
    <style>
        th.active .arrow {
            opacity: 1;
        }

        .arrow {
            display: inline-block;
            vertical-align: middle;
            width: 0;
            height: 0;
            margin-left: 5px;
            opacity: 0.66;
        }

        .arrow.asc {
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 4px solid #42b983;
        }

        .arrow.dsc {
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #42b983;
        }

        #search {
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans('messages.listProcessPanelLabel')}}</div>
                    <div class="panel-body">
                        @if(session()->has('updatemessage'))
                            <div class="alert alert-success">
                                {{trans('messages.updateProcessMessage')}}
                            </div>
                        @endif
                        <script type="text/x-template" id="grid-template">
                            <table class="table table-hover table-bordered">
                                <thead>
                                <tr>
                                    <th v-for="key in columns" @click="sortBy(key)" :class="{active: sortKey == key}">@{{key | capitalize}}<span class="arrow" :class="sortOrders[key] > 0 ? 'asc' : 'dsc'"></span>
                                    </th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="entry in data | filterBy filterKey | orderBy sortKey sortOrders[sortKey]">
                                    <td>
                                        <a href="/customers/list/process/@{{entry.id}}/entries" role="button">@{{entry.title}}</a>
                                        <br>
                                        <small>@{{entry.description}}</small>
                                    </td>
                                    <td>
                                        @{{entry.reference_id}}
                                    </td>
                                    <td v-if="entry.statusLight == 1">
                                        <svg width="50" height="50">
                                            <circle cx="25" cy="25" r="20" fill="red" />
                                        </svg>
                                    </td>
                                    <td v-if="entry.statusLight == 2">
                                        <svg width="50" height="50">
                                            <circle cx="25" cy="25" r="20" fill="yellow" />
                                        </svg>
                                    </td>
                                    <td v-if="entry.statusLight == 3">
                                        <svg width="50" height="50">
                                            <circle cx="25" cy="25" r="20" fill="green" />
                                        </svg>
                                    </td>
                                    <td>
                                        @{{entry.customerName}} <span class="badge">@{{entry.customerReferenceId}}</span>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href="/process/edit/@{{entry.id}}?r=all" role="button">Edit</a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </script>
                        <div id="app">
                            <form id="search">
                                Search <input name="query" v-model="searchQuery">
                            </form>
                            <br/>
                            <demo-grid  :data="{{$listProcesses}}"  :columns="gridColumns"  :filter-key="searchQuery"></demo-grid>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/js/vue.js"></script>
    <script src="/js/vue-resource.min.js"></script>
    <script>
        Vue.component('demo-grid', {
            template: '#grid-template',
            props: {
                data: Array,
                columns: Array,
                filterKey: String
            },
            data: function () {
                var sortOrders = {}
                this.columns.forEach(function (key) {
                    sortOrders[key] = 1
                })
                return {
                    sortKey: '',
                    sortOrders: sortOrders
                }
            },
            methods: {
                sortBy: function (key) {
                    this.sortKey = key
                    this.sortOrders[key] = this.sortOrders[key] * -1
                }
            }
        })

        // bootstrap the demo
        var demo = new Vue({
            el: '#app',
            data: {
                searchQuery: '',
                gridColumns: ['title', 'reference_id', 'statusLight', 'customerName'],
                gridData: null
            },

            created: function() {
                this.fetchData()
            },

            methods: {
                fetchData: function () {
                    var self = this;
                    $.get('/processes', function( data ) {
                        self.gridData = data;
                    });
                }
            }
        })

        $('div.alert').delay(3000).slideUp(300);
    </script>
@endsection