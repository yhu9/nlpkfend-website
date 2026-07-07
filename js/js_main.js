
        function contentChanger(){
            var contentname = document.getElementById('mySelect').value;
            var y = document.getElementById(contentname).innerHTML;
            document.getElementById("content").innerHTML = "" + y;
        }

        function contentChanger2(){
            var var1 = document.getElementById('view').value;
            var var2 = document.getElementById('mySelect').value;
            var contentname = var1.concat(var2);

            var page = document.getElementById(contentname).innerHTML;
            document.getElementById("content").innerHTML = "" + page;
        }

        function post(path, params, method) {
            method = method || "post"; // Set method to post by default if not specified.

            // The rest of this code assumes you are not using a library.
            // It can be made less wordy if you use one.
            var form = document.createElement("form");
            form.setAttribute("method", method);
            form.setAttribute("action", path);

            for(var key in params) {
                if(params.hasOwnProperty(key)) {
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", key);
                    hiddenField.setAttribute("value", params[key]);

                    form.appendChild(hiddenField);
                }
            }

            document.body.appendChild(form);
            form.submit();
        }


