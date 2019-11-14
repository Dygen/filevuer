<template>
  <div class="row justify-content-center browser col">
    <div class="card">
      <div class="card-header text-center large-font">
        <b>Remote Connections</b>
      </div>
      <div class="card-body">
        <div 
          v-if="connecting" 
          class="connecting">
          <div class="progress">
            <div 
              class="progress-bar progress-bar-striped progress-bar-animated" 
              role="progressbar" 
              aria-valuenow="100" 
              aria-valuemin="0" 
              aria-valuemax="100" 
              style="width: 100%"/>
          </div>
        </div>
        <div class="list-group large-font">
          <div 
            v-for="(items, index) in connectionList" 
            :key="index">
            <span 
              v-for="conn in items" 
              :key="conn">
              <a 
                href="#" 
                class="list-group-item list-group-item-action" 
                @click.stop.prevent="selectConnection(conn)">
                <span v-if="index == 'FTP'">
                  <span class="connection-type">
                    <font-awesome-icon 
                      icon="truck" 
                      size="lg" 
                      class="font-color" /> &nbsp; FTP: &nbsp;</span> {{ conn }}
                </span>
                <span v-if="index == 'S3'">
                  <span class="connection-type">
                    <font-awesome-icon 
                      icon="cloud" 
                      size="lg" 
                      class="font-color" /> &nbsp;&nbsp; S3: &nbsp;&nbsp;</span> {{ conn }}
                </span>
              </a>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import * as types from "../store/types";
import {
    mapState
} from "vuex";

export default {
    props: {
        connectionList: {
            default: null,
            type: Object
        }
    },
    data () {
        return {
            connecting: false
        }
    },
    computed: {
        ...mapState({
            isLoading: state => state.isLoading
        })
    },
    methods: {
        selectConnection (selectedConnection) {
            this.connecting = true;
            this.$store.dispatch(types.SET_CONNECTION, selectedConnection).then(
                response => {
                    this.connecting = false;
                    if (response) {
                        this.$store.commit(types.SET_CONNCTION_NAME, selectedConnection);
                        this.$emit("setLogin", true);
                    }
                }
            );
        }
    }
};
</script>
