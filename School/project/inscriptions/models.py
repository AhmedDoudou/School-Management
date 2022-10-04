from django.db import models
from django.contrib.auth.models import AbstractUser

class CustomUser(AbstractUser):
    user= (
        ('1', 'HOD'),
        ('2', 'STAFF'),
        ('3', 'STUDENT')
    )

    user_type = models.CharField(choices=user, max_length=50, default=1)
    profile_pic =models.ImageField(upload_to='customusers')

class Course(models.Model):
    name = models.CharField(max_length=100)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return self.name

class Session(models.Model):
    session_start = models.CharField(max_length=100)
    session_end = models.CharField(max_length=100)

class Student(models.Model):
    first_name = models.CharField(max_length=50)
    last_name = models.CharField(max_length=50, blank=True)
    tiny = "TM"
    mini = "MM"
    champions ="MC"
    grades = [
        (tiny,"Tiny Makers" ),
        (mini, "Mini Makers"),
        (champions, "Makers Champions"),
    ]
    birthday = models.CharField(max_length=100, default="01/01/2022")
    grade = models.CharField(max_length=10, choices=grades, default=mini)
    address = models.CharField(max_length=200, default="Casablanca")
    email = models.EmailField(max_length=100, default="maker@makerlab.ma")
    mobile = models.CharField(max_length=100, default="0621877106")
    profile_pic = models.ImageField(upload_to="profiles", blank=True, null=True)
    created_at = models.DateTimeField (auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)



    def __str__(self):
        return self.first_name+' '+self.last_name

class Program(models.Model):
    stemQuest = "SQP"
    on_choice = "OCC"
    holidays_camps = "HC"
    categories = [(stemQuest, 'StemQuest Program'), (on_choice, 'On Choice Camps'), (holidays_camps, 'Holidays Camps')]
    label = models.CharField(max_length=100)
    description = models.CharField(max_length=200)
    category =models.CharField(max_length=5, choices=categories, default=stemQuest,)

    def __str__(self):
        return self.label

class Membership(models.Model):
    monthly = "M"
    trimestrial = "T"
    annual = "A"
    period_choices = [(monthly, "MONTHLY"), (trimestrial, "TRIMESTRIAL" ),(annual, "ANNUAL")]
    label = models.CharField(max_length=100)
    membership_period =models.CharField(max_length=50, choices=period_choices, default=trimestrial,)
    def __str__(self):
        return self.label

class Inscription(models.Model):
    student = models.ForeignKey(Student, on_delete=models.CASCADE)
    course = models.ForeignKey(Course, on_delete=models.CASCADE)
    session = models.ForeignKey(Session, on_delete=models.CASCADE)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

class Registration(models.Model):
    cash = "CASH"
    cheque = "CHEQUE"
    card = "CREDIT CARD"
    payment_choices = [(cash, "CASH"), (cheque, "CHEQUE"), (card, "Bank Credit Card")]

    student = models.ForeignKey(Student, on_delete=models.CASCADE)
    program = models.ForeignKey(Program, on_delete=models.CASCADE)
    membership = models.ForeignKey(Membership, on_delete=models.CASCADE)
    payment_method = models.CharField(max_length=50, choices=payment_choices, default=cash, )
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)


    def __str__(self):
        return self.student.first_name+' '+self.student.last_name +' | '+self.program.label

class Payment(models.Model):
    cash = "CASH"
    cheque = "CHEQUE"
    card = "CREDIT CARD"
    payment_choices = [(cash, "CASH"), (cheque, "CHEQUE"), (card, "Bank Credit Card")]
    student = models.ForeignKey(Student, on_delete=models.CASCADE)
    membership = models.ForeignKey(Membership, on_delete=models.CASCADE)
    payment_method = models.CharField(max_length=50, choices=payment_choices, default=cash, )
    created_by = models.CharField(max_length=100)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)


    def __str__(self):
        return self.created_by+' | '+str(self.created_at)

class Parent(models.Model):
    GENDER_C      = (('Father', 'Father'),('Mother', 'Mother'))
    parent_gender = models.CharField( max_length=10,choices=GENDER_C,default='Father')
    fullname      = models.CharField( max_length=30)
    phone         = models.IntegerField()
    address       = models.CharField( max_length=50)
    description   = models.TextField(max_length=100, null=True)
    

    def __str__(self):
        return self.fullname