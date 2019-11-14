<template>
  <div class="col col-editor">
    <div id="editor">{{ contents }}</div>
    <div id="hide">
      <button 
        type="button" 
        class="btn btn-default btn-sm" 
        title="Hide editor" 
        @click="hide">
        <font-awesome-icon icon="arrow-right" />
      </button>
    </div>
    <div id="statusbar">
      <div class="text-center">
        <button 
          v-show="openFile !== null" 
          type="button" 
          class="btn btn-primary" 
          title="Save file" 
          @click="save">
          <font-awesome-icon icon="save" /> Save file
        </button>
      </div>

    </div>
  </div>
</template>

<script>
import * as types from '../store/types';
import ace from 'ace-builds';
import 'ace-builds/src-noconflict/ext-language_tools.js';
import 'ace-builds/src-noconflict/ext-modelist.js';
import 'ace-builds/src-noconflict/theme-tomorrow_night.js';
import {
    mapActions,
    mapState
} from 'vuex';

export default {
    data () {
        return {
            editor: null
        }
    },
    computed: {
        ...mapState({
            contents: state => state.editorContents,
            contentsChanged: state => state.editorContentsChanged,
            openFile: state => state.openFile,
        })
    },    
    watch: {
        contentsChanged () {
            this.editor.setValue(this.contents, -1);
        },
        openFile (newValue, oldValue) {
            if (oldValue !== newValue && newValue !== null) {
                let modelist = ace.require('ace/ext/modelist');
                let mode = modelist.getModeForPath(newValue).mode;
                this.editor.getSession().setMode(mode);
            }
        }
    },
    mounted () {
        this.editor = ace.edit('editor');
        this.editor.setOptions({
            theme: 'ace/theme/tomorrow_night',
            highlightActiveLine: true,
            displayIndentGuides: true,
            enableBasicAutocompletion: true,
            tabSize: 4
        });
        this.editor.$blockScrolling = Infinity;
    },
    destroyed () {
        this.editor.destroy()
    },
    methods: {
        ...mapActions({
            putContents: types.PUT_CONTENTS
        }),
        setEditorVisibility (visibility) {
            this.$store.commit(types.SET_EDITOR_VISIBILITY, visibility)
        },
        save () {
            this.putContents(this.editor.getValue());
        },
        hide () {
            this.setEditorVisibility(false);
        }
    },    
}
</script>