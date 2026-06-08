<div class="right-bar">
    <button class="right-bar-toggle-btn">
        <i class="fas fa-chevron-left"></i>
    </button>

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
.right-bar {
    position: fixed;
    z-index: 50;
    right: -50px;
    top: 70px;
    width: 50px;
    min-height: 150px;
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

.right-bar-toggle-btn {
    position: absolute;
    top: 50%;
    left: -15px;
    transform: translateY(-50%);

    width: 20px;
    height: 32px;
    border: none;
    border-radius: 0.5rem 0 0 0.5rem;

    display: flex;
    align-items: center;
    justify-content: center;

    background-color: #1a1f2e;
    color: #a0aec0;
    box-shadow: -4px 4px 6px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    padding: 0;
}

.right-bar-toggle-btn i {
    font-size: 12px;
    padding-right: 2px;
    transition: transform 0.3s ease, color 0.2s ease;
}
.right-bar:hover .right-bar-toggle-btn {
     color: #fff;
}

@media (max-width: 768px) {
    .right-bar {
        display: none;
    }
}
</style>
@endonce
