<div class="right-bar">
    <div class="right-bar__inner">
        <div class="text-white flex flex-col items-center">
            <div class="p-2">
                <a href="{{route('chat.index')}}">
                    <i class="fas fa-comments text-pink-500 text-lg"></i>
                </a>
            </div>

            <div class="p-2">
                <button type="button" class="background-none outline-none" onclick="openBackgroundSelector()">
                    <i class="fas fa-image text-pink-500 text-lg"></i>
                </button>
            </div>
        </div>
    </div>
</div>


@once
<style>
/* fixed z-50 right-[-50px] top-[50px] text-black w-[50px] min-h-[150px] */
.right-bar {
    position: fixed;
    z-index: 50;
    right: -50px;
    top: 70px;
    width: 50px;
    min-height: 150px;
    background-color: #fff;
    /* border-radius: 0.5rem; */
    border-top-left-radius: 0.5rem;
    border-bottom-left-radius: 0.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: right 0.3s ease;
        background: linear-gradient(to right, #1a1f2e 0%, #161b28 100%);
}
.right-bar:hover {
    right: 0;
}
.right-bar::before {
    content: '';
    position: absolute;
    left: -20px;
    top: 50%;
    transform: translateY(-50%);
    /* width: 0;
    height: 0; */
    padding:5px;
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    border-right: 10px solid #000;
}
@media (max-width: 768px) {
    .right-bar {
        display: none;
    }
}
</style>
@endonce
