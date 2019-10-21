<template>
    <div class="row">
        <div class="col-6">
            Client view
        </div>
        <div class="col-6">
            <button @click="callAgent" class="btn btn-secondary" :disabled="buttonDisabled">Call Agent</button>
        </div>
        <div class="col-12 mt-5">
            <div>INFO: {{ infoText }}</div>
        </div>
        <div class="col-12 mt-5">
            <div id="remote-media-div"></div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                token: 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImVkNmJmMjFhZTUyMWJmMGU2ZmNlMGI0MzdmZjBlMWEzNTEwMjFmNzEzNzEyMWQ0MGQwYjRiMjVlY2VhMWI4NmQwZGQxZDRjODEzMWE0ZmU4In0.eyJhdWQiOiIxIiwianRpIjoiZWQ2YmYyMWFlNTIxYmYwZTZmY2UwYjQzN2ZmMGUxYTM1MTAyMWY3MTM3MTIxZDQwZDBiNGIyNWVjZWExYjg2ZDBkZDFkNGM4MTMxYTRmZTgiLCJpYXQiOjE1NzE2NTkxMTgsIm5iZiI6MTU3MTY1OTExOCwiZXhwIjoxNTcxNzQ1NTE4LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.delpzHj55PxEublvWx2cbyVfgTgymY3I5kYMsPXmLS-gFltwxszXc1VY_yp4cM2bUuijKAnaf-z45BsOiLno-DdA20WFs9Q-EgiXDfXTbgV4SSrs_mhUeGW2rxjomJGLL2KhkTKzevNswzYYwp7VhafbXf5aJ1CseTLLpeQI04GlJsWixZe7qmsW5uy1AjJiBzRKiCbGWTH75ypRRHz0rtcHdOSw39oxa_SBkI06P_tgNeKokWxesijCakVJgfQDZLoHVyiIUSK1tHMCdq7-kEDNyW9Yq9QP5QuKkIssBvJ904f86sKbGrdYG0WeSbyPfq4ISIwqpKBa3i908P8b9LStr3rw8XZHwMAtfqTKDFmKpu9lpzMjEv1O_2GdvGeTHj32MGpkD7QJt97cln6F716eGA93J8DzMqsAzr7sJEPf7Am1GcsVpSvvqfRHfbORGSQjkCz9NLDkDlTrXzk4lIPgm3SvdLrnysHUHJbG_9So5BPvcv1LSa5H0GINNmJiIA4ltcqtLhDujRjjwpKGuc8hLJJB5dysCCf_sVIg6KoIbfnVjRFYpOuITJmq_FhNE5tV5htsIylCw4rJ9xvnlJl-M1z4aTPX7vJVfPys7CIcgw46Aelgrb1Bj0iHfKSsfwBief3QxVzI00t_rkopepEFKp4MN6Kc-zQg_GHtHss',
                infoText: '',
                client: {},
                agent: {},
                buttonDisabled: false
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
            callAgent() {
                axios.post('http://api.anythingxxx.local/v1/video-calls/qnwmkv5704blag6r', {}, {
                    headers: {'Authorization': this.token},
                    useCredentials: true
                })
                    .then(response => {
                        this.buttonDisabled = true;

                        this.client.twilioRoomName = response.data.roomName;
                        this.client.twilioToken = response.data.clientToken;

                        this.agent.twilioRoomName = response.data.roomName;
                        this.agent.twilioToken = response.data.agentToken;

                        this.fireEvent(this.agent);

                        this.infoText = 'Got token and room name ' + this.client.twilioRoomName;

                        this.callTwilio();


                        console.log(response);
                    })
                    .catch(error => {
                        this.infoText = 'Error ' + error.message;
                        console.log(error);
                    });
            },
            fireEvent(agent) {
                axios.get('/call-agent?token=' + agent.twilioToken + '&room=' + agent.twilioRoomName)
                    .then(response => {
                        console.log(response);
                    })
                    .catch(error => {
                        this.infoText = 'Error ' + error.message;
                        console.log(error);
                    });
            },
            callTwilio() {
                const {connect} = require('twilio-video');

                connect(this.client.twilioToken, {
                    name: this.client.twilioRoomName,
                    audio: true,
                    video: {width: 640},
                    tracks: []
                }).then(room => {
                    console.log(`A CLIENT connected to room ${room}`);
                    room.on('participantConnected', participant => {
                        console.log(`Agent that is connected: ${participant}`);

                        participant.tracks.forEach(publication => {
                            if (publication.isSubscribed) {
                                const track = publication.track;
                                document.getElementById('remote-media-div').appendChild(track.attach());
                            }
                        });

                        participant.on('trackSubscribed', track => {
                            document.getElementById('remote-media-div').appendChild(track.attach());
                        });

                    });
                }, error => {
                    console.error(`Unable to connect to Room: ${error.message}`);
                });
            }

        }
    }
</script>
