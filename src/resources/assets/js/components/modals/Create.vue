<template>
  <div>
    <b-modal 
      id="fvCreate" 
      ref="fvCreate" 
      size="sm" 
      @shown="focusName">
      <h4 
        slot="modal-header" 
        class="fv-modal-header">Create a new {{ newFileFolder }}</h4>
      <div class="row">
        <div class="col">
          <b-form-input 
            ref="nameFocus" 
            v-model="name" 
            type="text" 
            class="form-control" 
            placeholder="Enter name"/>
        </div>
      </div>

      <div 
        slot="modal-ok" 
        size="sm" 
        @click.prevent="confirm()">
        Create
      </div>

      <div 
        slot="modal-cancel" 
        size="sm" 
        @click.prevent="reset()">
        Cancel
      </div>

    </b-modal>
  </div>
</template>

<script>
import * as types from '../../store/types';
import {
    mapActions,
    mapState
} from 'vuex';
export default {
    data () {
        return {
            name: '',
        }
    },
    computed: {
        ...mapState({
            newFileFolder: state => state.newFileFolder
        }),
    },    
    methods: {
        ...mapActions({
            create: types.CREATE_NEW
        }),
        confirm () {
            this.create({
                type: this.newFileFolder,
                path: this.name
            }).then(this.reset);
        },
        reset () {
            this.name = '';
        },
        focusName () {
            this.$refs.nameFocus.focus()
        }
    },
}
</script>