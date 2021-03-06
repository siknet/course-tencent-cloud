{% extends 'templates/main.volt' %}

{% block content %}

    {%- macro source_type_info(value) %}
        {% if value == 1 %}
            免费
        {% elseif value == 2 %}
            <span class="layui-badge layui-bg-orange">付费</span>
        {% elseif value == 3 %}
            导入
        {% endif %}
    {%- endmacro %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a class="kg-back"><i class="layui-icon layui-icon-return"></i>返回</a>
                {% if course %}
                    <a><cite>{{ course.title }}</cite></a>
                {% endif %}
                <a><cite>学员管理</cite></a>
            </span>
        </div>
    </div>

    <table class="layui-table kg-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col width="12%">
        </colgroup>
        <thead>
        <tr>
            <th>基本信息</th>
            <th>学习情况</th>
            <th>成员来源</th>
            <th>有效期限</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set learning_url = url({'for':'admin.student.learning'},{'course_id':item.course_id,'user_id':item.user_id,'plan_id':item.plan_id}) %}
            {% set list_by_course_url = url({'for':'admin.student.list'},{'course_id':item.course.id}) %}
            {% set list_by_user_url = url({'for':'admin.student.list'},{'user_id':item.user_id}) %}
            {% set edit_url = url({'for':'admin.student.edit'},{'relation_id':item.id}) %}
            <tr>
                <td>
                    <p>课程：<a href="{{ list_by_course_url }}">{{ item.course.title }}（{{ item.course.id }}）</a></p>
                    <p>学员：<a href="{{ list_by_user_url }}">{{ item.user.name }}（{{ item.user.id }}）</a></p>
                </td>
                <td>
                    <p>进度：<a href="javascript:" class="kg-learning" title="学习记录" data-url="{{ learning_url }}">{{ item.progress }}%</a></p>
                    <p>时长：{{ item.duration|duration }}</p>
                </td>
                <td>{{ source_type_info(item.source_type) }}</td>
                <td>
                    <p>开始：{{ date('Y-m-d H:i:s',item.create_time) }}</p>
                    <p>结束：{{ date('Y-m-d H:i:s',item.expiry_time) }}</p>
                </td>
                <td class="center">
                    <div class="layui-dropdown">
                        <button class="layui-btn layui-btn-sm">操作 <i class="layui-icon layui-icon-triangle-d"></i></button>
                        <ul>
                            <li><a href="{{ edit_url }}">编辑学员</a></li>
                            <li><a href="javascript:" class="kg-learning" data-url="{{ learning_url }}">学习记录</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'form'], function () {

            var $ = layui.jquery;

            $('.kg-learning').on('click', function () {
                var url = $(this).data('url');
                layer.open({
                    id: 'xm-course',
                    type: 2,
                    title: '学习记录',
                    resize: false,
                    area: ['900px', '450px'],
                    content: [url]
                });
            });

        });

    </script>

{% endblock %}