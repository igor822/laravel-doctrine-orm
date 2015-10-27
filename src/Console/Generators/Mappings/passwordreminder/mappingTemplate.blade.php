{{$namespace}}\PasswordReminder:
    type: entity
    table: password_resets
    id:
      email:
        type: string
        nullable: false
        column: email
        id: true
        generator:
          strategy: NONE
    fields:
      token:
        type: string
        nullable: false
        column: token
      createdAt:
        type: timestamp
        nullable: false
        column: created_at