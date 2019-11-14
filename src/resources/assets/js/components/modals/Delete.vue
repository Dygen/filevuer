<template>

  <div>
    <b-modal 
      id="fvDelete" 
      ref="fvDelete" 
      :hide-header="true" 
      size="sm" 
      ok-variant="danger">
      <div class="row">
        <div class="col text-center">
          <div 
            class="alert alert-danger" 
            role="alert">
            Do you really want to delete the selected files and/or folders?
          </div>
        </div>
      </div>

      <div 
        slot="modal-ok" 
        size="sm" 
        @click.prevent="confirm()">
        Yes, delete the files/folders
      </div>

    </b-modal>
  </div>
    
</template>

<script>
import * as types from '../../store/types';
import {
    mapActions,
} from 'vuex';
export default {
    data () {
        return {
            disabled: false
        }
    },
    methods: {
        ...mapActions({
            deleteSelected: types.DELETE_SELECTED
        }),
        confirm () {
            this.disabled = true;
            this.deleteSelected().then(() => {
                this.disabled = false;
            });
        }
    },
}
</script>