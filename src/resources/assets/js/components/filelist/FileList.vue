<template>
  <div>

    <div class="row">

      <div :class="{'col-10': isRootLevel === false, 'col': isRootLevel === true}">
        <b-input-group class="filter-bar">
          <b-input-group-prepend is-text>
            <font-awesome-icon icon="search" />
          </b-input-group-prepend>

          <b-form-input 
            v-model="searchTerm"
            type="text"
            placeholder="Filter ..."/>
        </b-input-group>
      </div>

      <div 
        v-if="isRootLevel === false"
        class="col-2" >
        <b-button 
          variant="secondary" 
          size="sm"
          class="pull-right" 
          @click.prevent="levelUp">
          ... Up One Level
          <font-awesome-icon icon="level-up-alt" />
        </b-button>
      </div>

    </div>

    <div class="row">

      <div class="col">

        <vue-good-table 
          id="filelist-table"
          :columns="columns" 
          :rows="listing" 
          :select-options="{
            enabled: true,
            selectOnCheckboxOnly: true, // only select when checkbox is clicked instead of the row
          }"
          :search-options="{
            enabled: true,
            placeholder: 'Filter...',
            externalQuery: searchTerm
          }"
          style-class="vgt-table condensed"
          @on-selected-rows-change="selectionChanged"
          @on-cell-click="click">
          <template 
            slot="table-row" 
            slot-scope="props">
            <span v-if="props.column.field == 'type'">
              <span class="col text-center">
                <font-awesome-icon 
                  :icon="icon(props.row.type)" 
                  size="2x" />
              </span>
            </span>
            <span v-else-if="props.column.field == 'basename'">
              <div class="row">
                <div class="col-11">
                  {{ props.row.basename }}
                </div>  
                <div class="col">
                  <font-awesome-icon 
                    v-if="edit(props.row.extension)" 
                    icon="edit" />
                </div>
              </div>
            </span>          
            <span v-else>
              {{ props.formattedRow[props.column.field] }}
            </span>          
          </template>

        </vue-good-table>

      </div>

    </div>



  </div>
</template>

<script>
    import { VueGoodTable } from 'vue-good-table';
    import * as types from '../../store/types';
    import {
        mapActions,
        mapState
    } from 'vuex';

    export default {
        components: {
            VueGoodTable
        },
        data () {
            return {
                columns: [
                    {
                        label: 'Type',
                        field: 'type',
                        sortable: false,
                        globalSearchDisabled: true,
                    },
                    {
                        label: 'Filename',
                        field: 'basename',
                        type: 'string',
                        width: '70%',
                        tdClass: 'set-pointer'
                    },
                    {
                        label: 'Visibility',
                        field: 'visibility',
                        globalSearchDisabled: true,
                    },
                    {
                        label: 'Filesize',
                        field: 'size',
                        globalSearchDisabled: true,
                    },
                ],
                searchTerm: '',
                editable: ['txt','php','html','htm','css','js'],
            };
        },
        computed: {
            ...mapState({
                listing: state => state.files,
                path: state => state.path,
            }),
            isRootLevel () {
                return this.path === '/';
            },          
        }, 
        methods: {
            ...mapActions({
                levelUp: types.LEVEL_UP,
                fetchContents: types.FETCH_CONTENTS,
                changeDirectoryRelative: types.CHANGE_DIRECTORY_RELATIVE,
                selectionChanged: types.SET_SELECTED_FILES
            }),
            icon(type) {
                return type === 'file' ? 'file-alt' : 'folder'
            }, 
            click(params) {
                this.searchTerm = '';
                if (params.row.type === 'file') {
                    if(this.editable.includes(params.row.extension)) {
                        this.fetchContents(params.row.basename);
                    }

                } else {
                    this.searchTerm = '';
                    this.changeDirectoryRelative(params.row.basename)
                }
            },
            edit(extension) {
                return this.editable.includes(extension);
            },             
        },
    }
</script>