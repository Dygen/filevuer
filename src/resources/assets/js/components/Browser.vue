<template>
  <div class="">
    <div class="row">
      <div class="browser rounded col col-xl-6 offset-xl-3 bg-white">
        <breadcrumbs/>
        <toolbar/>
        <file-list/>
      </div>
    </div>
  </div>
</template>

<script>
import Breadcrumbs from './layout/Breadcrumbs.vue';
import Toolbar from './layout/Toolbar.vue';
import FileList from './filelist/FileList.vue';
import * as types from '../store/types';
import store from '../store';
import {
    mapState
} from 'vuex';

export default {
    components: {
        Breadcrumbs,
        Toolbar,
        FileList
    },    
    props: {
        selected: {
            default: '',
            type: String
        }
    },
    computed: {
        ...mapState({
            isLoading: state => state.isLoading
        })
    },
    created () {
        store.dispatch(types.FETCH_FILES, '/');
    },
    mounted: function () {
        if(this.selected !== '') {
            store.commit(types.SET_CONNCTION_NAME, this.selected);
        }
    }
}
</script>