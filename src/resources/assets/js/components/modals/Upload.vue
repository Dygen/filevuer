<template>

  <div>
    <b-modal 
      id="fvUpload" 
      ref="fvUpload">
      <h4 
        slot="modal-header" 
        class="fv-modal-header">Upload a new file or zipped folder</h4>
      <div class="row">
        <div class="col">
          <b-form-file 
            id="fv-fileUpload" 
            ref="fileinput" 
            v-model="files" 
            multiple/>

          <div class="checkbox zip-checkbox">
            <label>
              <input 
                v-model="extract" 
                type="checkbox"> Extract ZIP archives on server
            </label>
          </div>
        </div>

      </div>

      <div 
        slot="modal-ok" 
        size="sm" 
        @click.prevent="confirm()">
        Upload files/folders
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
import * as types from "../../store/types";
import {
    mapActions,
} from "vuex";

export default {
    data () {
        return {
            files: [],
            extract: false,
        };
    },
    methods: {
        ...mapActions({
            upload: types.UPLOAD
        }),
        confirm () {
            let files = this.files;
            if (files.length < 1) return false;
            this.upload({
                files,
                extract: this.extract
            }).then(() => {
                this.extract = false;
                this.reset;
            });
        },
        reset () {
            this.$refs.fileinput.reset();
        },
    },
};
</script>