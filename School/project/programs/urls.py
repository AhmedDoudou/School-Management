from django.urls import path
from .views import ProgramCreateView, ProgramListView,ProgramUpdateView,ProgramDeleteView

app_name = "program"
urlpatterns = [
    # path("", DashbordView.as_view(), name="home"),
    path("", ProgramListView.as_view(), name="list"),
    path("add/", ProgramCreateView.as_view(), name="create"),
    path("update/<int:pk>/", ProgramUpdateView.as_view(), name="update"),
    path("delete/<int:pk>/", ProgramDeleteView.as_view(), name="delete"),


]