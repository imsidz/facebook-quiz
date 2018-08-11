@if(!empty($mainBtnColor))
    .btn-primary {
        background-color: transparent;
    }
    .btn-primary span{
        background-color: {{$mainBtnColor}};
    }
    .grid-box .boxed.question-choice:hover {
        background: {{$mainBtnColor}};
        color: #fff;
        border-color: transparent;
    }
    .main-btn-color {
        color: {{$mainBtnColor}} !important;
    }
    .bg-main-btn-color {
        background-color : {{$mainBtnColor}} !important;
    }
@endif

@if(!empty($linkColor))
    a{
        color: {{$linkColor}};
    }
    a:hover, a:focus {
        color: #222;
        text-decoration: none;
    }
    .link-color {
        color: {{$linkColor}} !important;
    }
    .bg-link-color {
        background-color: {{$linkColor}} !important;
    }
@endif

#topmenu .dropdown {
    background-color: {{$navbarColor}};
}

#topmenu .dropdown > li{
    border-right: 1px solid rgba(255, 255, 255, 0.24);
}

#topmenu .dropdown > li a{
    color: #ffffff;
}

#topmenu #headerUserLoginLink > span {
    background: #fff;
    color: #222;
}

.navbar-color {
    color: {{$navbarColor}} !important;
}
.bg-navbar-color {
    background-color: {{$navbarColor}} !important;
}