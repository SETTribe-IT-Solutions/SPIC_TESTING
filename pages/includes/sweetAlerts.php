<html>
    <head>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
         <!-- <link href="https://sweetalert2.github.io/styles/bootstrap4-buttons.css" rel="stylesheet"/>-->
          <meta name="viewport" content="width=device-width, initial-scale=1.0">

    </head>
<body>
    
</body>

      
      <script>
         
//Function for sweetalert

function error_alert(usertitle)
{
    Swal.fire({
         title: usertitle,
        icon: 'error',
        confirmButtonText: 'Ok'
         }).then((result) => {
  if (result.isConfirmed) {
  }else{
  }
  })
}

function sweetalert_w(usertitle,pagename)
{
    Swal.fire({
         title: usertitle,
        icon: 'warning',
        confirmButtonText: 'Ok'
         }).then((result) => {
  if (result.isConfirmed) {
   window.location = pagename;
  }else{
  window.location = pagename;
  }
  })
}

function logoutFun(location) {
        Swal.fire({
            title: 'Are you sure want to logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = location;
            }
        })
    }
function deleteFun(location) {
        Swal.fire({
            title: 'Are You Sure You Want to Delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = location;
            }
        })
    }
    function deleteImg(location) {
    Swal.fire({
        title: 'फोटो डिलीट करायचा आहे का ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'होय, डिलीट करा!',
        cancelButtonText: 'नाही' // Added cancelButtonText option
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = location;
        }
    })
}

function deleteDoc(location) {
    Swal.fire({
        title: 'डॉक्युमेंट डिलीट करायचा आहे का ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'होय, डिलीट करा!',
        cancelButtonText: 'नाही' // Added cancelButtonText option
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = location;
        }
    })
}

function deletefood(location) {
    Swal.fire({
        title: 'आहार डिलीट करायचा आहे का ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'होय, डिलीट करा!',
        cancelButtonText: 'नाही' // Added cancelButtonText option
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = location;
        }
    })
}
function deleteYadi(location) {
    Swal.fire({
        title: 'तुम्हाला खात्री आहे का ?<br><br ><h3>तुम्ही हा डेटा स्थायीपणे डिलीत करू इच्छिता का ?</h3> ',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'होय, डिलीट करा!',
        cancelButtonText: 'नाही' // Added cancelButtonText option
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = location;
        }
    })
}
function deleteVipYadi(location) {
    Swal.fire({
        title: 'तुम्हाला खात्री आहे का ?<br><br ><h3>तुम्ही हा डेटा स्थायीपणे डिलीत करू इच्छिता का ?</h3> ',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'होय, डिलीट करा!',
        cancelButtonText: 'नाही' // Added cancelButtonText option
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = location;
        }
    })
}

   function callFun(phoneNumber) {
    Swal.fire({
        title: 'Are you sure you want to call?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'tel:' + phoneNumber;
        }
    })
}
 function callFun1(phoneNumber) {
    Swal.fire({
        title: 'Are you sure you want to call?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'tel:' + phoneNumber;
        }
    })
}

          
          function warnFun(location) {
        Swal.fire({
            title: 'Are you sure want to Reject?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                location;
            }
        })
    }


function sweetalert_sw(usertitle)
{
    Swal.fire({
         title: usertitle,
        icon: 'warning',
        confirmButtonText: 'Ok'
         }).then((result) => {
  if (result.isConfirmed) {
   window.location = "#";
  }else{
  window.location = "#";
  }
  })
}
          
//Function for sweetalert
function sweetalert(usertitle,pagename)
{
    Swal.fire({
         title: usertitle,
        icon: 'success',
        confirmButtonText: 'Ok'
         }).then((result) => {
  if (result.isConfirmed) {
   window.location = pagename;
  }else{
  window.location = pagename;
  }
  })
}

//Out of office
function erroralertsorry(usertitle,pagename,reason)
{
    Swal.fire({
        title: usertitle,
        icon: 'error',
        text:reason,
        confirmButtonText: 'Ok'
         }).then((result) => {
  if(result.isConfirmed){
      window.location = pagename;
  }else{
      window.location = pagename;
  }
  })
}

//Out of office Forced
function erroralertsorry_force(usertitle,pagename,forced_link,reason)
{
    Swal.fire({
         title: usertitle,
        icon: 'error',
        text:reason,
        confirmButtonText: 'Ok',
        //showDenyButton : true,
        //denyButtonText: 'Do Force',
        
         }).then((result) => {
  if(result.isConfirmed){
      window.location = pagename;
  }else if(result.isDenied){
      window.location = forced_link;
  }else{
      window.location = pagename;
  }
  })
}


//Function for error
function erroralert(usertitle,pagename)
{
    Swal.fire({
         title: usertitle,
        icon: 'error',
        confirmButtonText: 'Ok'
         }).then((result) => {
  if (result.isConfirmed) {
   window.location = pagename;
  }else{
  window.location = pagename;
  }
  })
}


function toast(title,icon){
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})

Toast.fire({
  icon: icon,
  title: title
})
}
          
function deleteButton(buttonSelector) {
    // Add event listeners to all buttons matching the selector
    document.querySelectorAll(buttonSelector).forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default link behavior

            const deleteUrl = this.getAttribute('data-url'); // URL to perform the action
            const deleteMsg = this.getAttribute('data-msg') || "Are you sure you want to delete this?";

            // Show confirmation dialog
            Swal.fire({
                title: 'Confirm Deletion',
                text: deleteMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the delete URL if confirmed
                    window.location.href = deleteUrl;
                }
            });
        });
    });
}

// Initialize the delete button functionality
document.addEventListener('DOMContentLoaded', function () {
    deleteButton('.delete-button');
});


</script>

<?php
function showSweetAlert($title, $text, $icon, $redirectUrl = null)
{
    echo "<script>
        Swal.fire({
            title: '$title',
            text: '$text',
            icon: '$icon',
            allowOutsideClick: false,
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                " . ($redirectUrl ? "window.location.href = '$redirectUrl';" : "") . "
            }
        });
    </script>";
}
?>
  <body>
</body>
</html>