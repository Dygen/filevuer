<template>
  <div>

    <div class="row">
      <div class="col"/>
      <div class="col text-right connection-name">
        <b-alert 
          show 
          variant="dark" 
          class="text-center">{{ connectionName }}</b-alert>
      </div>
      <div class="col text-right logout-button">
        <a 
          href="/filevuer/logout" 
          class="btn btn-outline-primary btn-sm" 
          role="button">Logout</a>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <ol class="breadcrumb">
          <li>
            <a @click="cd('/')">
              <font-awesome-icon 
                icon="home" 
                size="lg"/> 
            </a>
          </li>
          <li 
            v-for="(breadcrumb, index) in breadcrumbs" 
            :class="{ active: isLast(index) }" 
            :key="index" 
            class="breadcrumb-item">
            <a 
              v-if="!isLast(index)" 
              @click="cd((index === 0 ? '' : '/') + breadcrumb.path)">
              {{ breadcrumb.label }}
            </a>
            <span v-else>
              {{ breadcrumb.label }}
            </span>
          </li>
        </ol>
      </div>
    </div>

  </div>

</template>

<script>
    import * as types from '../../store/types';
    import {
        mapActions,
        mapState
    } from 'vuex';

    export default {
        data() {
            return {
                index: null,
            }
        },
        computed: {
            ...mapState({
                path: state => state.path,
                connectionName: state => state.connectionName
            }),
            breadcrumbs() {
                const root = [{
                    label: '',
                    path: ''
                }];
                let path = '';
                let mapItem = label => {
                    path += label + '/';
                    return {
                        label,
                        path
                    }
                };
                return root.concat(this.path.slice(1, -1).split('/').map(mapItem));
            }
        },        
        methods: {
            ...mapActions({
                cd: types.CHANGE_DIRECTORY
            }),
            isLast(index) {
                return index + 1 === this.breadcrumbs.length
            }
        },
    }
</script>
